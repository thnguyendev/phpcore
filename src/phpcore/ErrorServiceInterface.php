<?php
    namespace phpcore;

    interface ErrorServiceInterface {
        public function process(\Throwable $e);
    }
?>