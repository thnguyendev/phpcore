<?php
    namespace phpcore;

    use Exception;

    interface ErrorServiceInterface {
        public function process(Exception $exception);
    }
?>