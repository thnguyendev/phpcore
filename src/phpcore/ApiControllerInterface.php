<?php
    namespace phpcore;

    interface ApiControllerInterface {
        public function checkMethod();
        public function getApp();
        public function setApp($app);
        public function get();
		public function post();
		public function put();
		public function delete();
		public function patch();
		public function options();
    }
?>