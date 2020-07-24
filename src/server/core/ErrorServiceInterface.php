<?php
    namespace phpcore\core;

    use Exception;

    interface ErrorServiceInterface {
        public function process(Exception $exception);
    }
?>