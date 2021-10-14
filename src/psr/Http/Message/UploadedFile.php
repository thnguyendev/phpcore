<?php
    namespace Psr\Http\Message;

    class UploadedFile implements UploadedFileInterface {
        private $file;
        private $stream = null;
        private $moved = false;
        private $size;
        private $error;
        private $name;
        private $type;

        // todo: constructor get client filename from $_FILES[uploadFile][names]
        public function __construct(string $clientFilename) {
        }

        public function getStream() {
            if ($this->moved)
                throw new \RuntimeException(ErrorMessage::noStreamAvailable);
            if (!$this->stream) {
                $resource = fopen($this->file, 'r');
                if (!is_resource($resource))
                    throw new \RuntimeException(ErrorMessage::noStreamCreated);
            }
            return $this->stream;
        }

        public function moveTo($targetPath) {
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

        public function getSize() {
            return $this->size;
        }

        public function getError() {
            return $this->error;
        }

        public function getClientFilename() {
            return $this->name;
        }

        public function getClientMediaType() {
            return $this->type;
        }
    }
?>