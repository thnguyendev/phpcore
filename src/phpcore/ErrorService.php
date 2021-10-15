<?php
    namespace PHPCore;

    class ErrorService implements ErrorServiceInterface {
        public function process(\Throwable $e) {
            error_log($e->getMessage());
        }
    }
?>