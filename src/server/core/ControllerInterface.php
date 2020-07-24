<?php
    namespace phpcore\core;

    interface ControllerInterface {
        public function view();
        public function process();
        public function checkMethod();
        public function getApp();
        public function setApp($app);
    }
?>