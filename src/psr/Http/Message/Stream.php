<?php
namespace Psr\Http\Message;

class Stream implements StreamInterface {
    protected const READABLE = 
    [
        'r', 'w+', 'r+', 'x+', 'c+', 'rb', 'w+b', 'r+b', 'x+b', 'c+b', 'rt', 'w+t', 'r+t', 'x+t', 'c+t', 'a+'
    ];

    protected const WRITABLE = 
    [
        'w', 'w+', 'rw', 'r+', 'x+', 'c+', 'wb', 'w+b', 'r+b', 'x+b', 'c+b', 'w+t', 'r+t', 'x+t', 'c+t', 'a', 'a+'
    ];

    /* @var resource */ 
    protected $handle;
    /* @var array */
    protected $metadata = [];

    /*
        * Constructor of a stream
        * @param resource $handle
        * @throws \InvalidArgumentException for invalid handle
        */
    public function __construct($handle)
    {
        if (!is_resource($handle))
            throw new \InvalidArgumentException(ErrorMessage::invalidHandle);
        $this->handle = $handle;
        $this->metadata = stream_get_meta_data($handle);
    }

    public function __toString()
    {
        if (isset($this->handle))
        {
            if ($this->isSeekable())
                $this->seek(0);
        }
        return $this->getContents();
    }

    public function close()
    {
        if (isset($this->handle))
        {
            fclose($this->handle);
            unset($this->handle);
            unset($this->metadata);
        }
    }

    public function detach()
    {
        $handle = $this->handle;
        if (isset($this->handle))
        {
            unset($this->handle);
            unset($this->metadata);
        }
        return $handle;
    }

    public function getSize()
    {
        $size = null;
        if (isset($this->handle))
        {
            if (isset($this->metadata['uri']))
                clearstatcache(true, $this->metadata['uri']);
            $stat = fstat($this->handle);
            if (isset($stat['size']))
                $size = $stat['size'];
        }
        return $size;
    }

    public function tell()
    {
        $position = ftell($this->handle);
        if ($position === false) {
            throw new \RuntimeException(ErrorMessage::invalidPointer);
        }
        return $position;
    }

    public function eof()
    {
        return feof($this->handle);
    }

    public function isSeekable()
    {
        $seekable = false;
        if (isset($this->metadata['seekable']))
            $seekable = $this->metadata['seekable'];
        return $seekable;
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        if (!$this->isSeekable())
            throw new \RuntimeException(ErrorMessage::unseekable);
        if (fseek($this->handle, $offset, $whence) === -1)
            throw new \RuntimeException(sprintf(ErrorMessage::failedSeeking, $offset, $whence));
    }

    public function rewind()
    {
        $this->seek(0);
    }

    public function isWritable()
    {
        return in_array($this->metadata['mode'], self::WRITABLE);
    }

    public function write($string)
    {
        if (!$this->isWritable())
            throw new \RuntimeException(ErrorMessage::unwritable);
        $written = fwrite($this->handle, $string);
        if ($written === false)
            throw new \RuntimeException(ErrorMessage::failedWriting);
        return $written;
    }

    public function isReadable()
    {
        return in_array($this->metadata['mode'], self::READABLE);
    }

    public function read($length)
    {
        if (!$this->isReadable())
            throw new \RuntimeException(ErrorMessage::unreadable);
        $read = fread($this->handle, $length);
        if ($read === false)
            throw new \RuntimeException(ErrorMessage::failedReading);
        return $read;
    }

    public function getContents()
    {
        if (!$this->isReadable())
            throw new \RuntimeException(ErrorMessage::unreadable);
        $contents = stream_get_contents($this->handle);
        if ($contents === false)
            throw new \RuntimeException(ErrorMessage::failedReading);
        return $contents;
    }

    public function getMetadata($key = null)
    {
        $metadata = null;
        if ($key === null)
            $metadata = $this->metadata;
        else if (isset($this->metadata[$key]))
            $metadata = $this->metadata[$key];
        return $metadata;
    }
}
?>