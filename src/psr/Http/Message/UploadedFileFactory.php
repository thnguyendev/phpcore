<?php
namespace Psr\Http\Message;

class UploadedFileFactory implements UploadedFileFactoryInterface
{
    public function createUploadedFile
    (
        StreamInterface $stream,
        int $size = null,
        int $error = \UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ): UploadedFileInterface
    {
        if (!$stream->isReadable())
            throw new \InvalidArgumentException("Stream must be readable");
        return new UploadedFile($stream. $size, $error, $clientFilename, $clientMediaType);
    }
}
?>