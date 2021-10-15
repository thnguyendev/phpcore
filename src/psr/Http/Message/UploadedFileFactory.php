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
        ) : UploadedFileInterface
        {
            return new UploadedFile();
        }
    }
?>