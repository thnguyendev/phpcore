<?php
    namespace Phpcore;

    interface ErrorServiceInterface {
        public function process(\Throwable $e);
    }
?>