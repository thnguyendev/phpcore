<?php
    namespace phpcore;

    interface RequestServiceInterface {
        public function redirectToHttps();
        public function redirectToNoTrailingSlash();
        public function getHeader();
        public function getRequest();
        public function getServer();
        public function getHttps();
        public function getBody();
        public function getSegments();
        public function getQuerry();
        public function setQuerry($querry);
    }
?>