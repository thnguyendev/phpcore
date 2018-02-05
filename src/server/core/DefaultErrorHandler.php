<?php
    namespace phpcore\core;

    use Exception;

    class DefaultErrorHandler implements IErrorHandler {
        public function process($exception) {
            trigger_error($exception->getMessage());
            HttpCodes::internalServerError();
        }
    }
?>