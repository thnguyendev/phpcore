<?php
    namespace phpcore\core;

    interface IErrorHandler {
        public function Process($exception);
    }
?>