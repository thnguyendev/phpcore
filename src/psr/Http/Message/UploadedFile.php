<?php
namespace Psr\Http\Message;

class UploadedFile implements UploadedFileInterface
{
    protected $file;
    protected $stream = null;
    protected $moved = false;
    protected $size;
    protected $error;
    protected $name;
    protected $type;

    public function __construct
    (
        StreamInterface $stream,
        int $size = null,
        int $error = \UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    )
    {
        $this->stream = $stream;
        $this->size = $size;
        $this->error = $error;
        $this->name = $clientFilename;
        $this->type = $clientMediaType;
    }

    public function getStream()
    {
        if ($this->moved)
            throw new \RuntimeException(ErrorMessage::noStreamAvailable);
        if (!$this->stream) {
            $resource = fopen($this->file, 'r');
            if (!is_resource($resource))
                throw new \RuntimeException(ErrorMessage::noStreamCreated);
        }
        return $this->stream;
    }

    public function moveTo($targetPath)
    {
        if (!is_string($targetPath))
            throw new \InvalidArgumentException(ErrorMessage::invalidTargetPath);
        if ($this->moved)
            throw new \RuntimeException(ErrorMessage::uploadedFileMoved);
        if (is_uploaded_file($this->file))
            $success = move_uploaded_file($this->file, $targetPath);
        else
            $success = rename($this->file, $targetPath);
        if (!$success)
            throw new \RuntimeException(ErrorMessage::errorMoving);
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getClientFilename()
    {
        return $this->name;
    }

    public function getClientMediaType()
    {
        return $this->type;
    }
}
?>