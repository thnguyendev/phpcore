<?php
    namespace phpcore;

    interface AppInterface {
        public function process();
        public function addService(object $service);
        public function getService(string $serviceName);
        public function enableCors($origins = "", $methods = "", $headers = "");
        public function allowCors();
        public function getAllowedOrigins();
        public function getAllowedMethods();
        public function getAllowedHeaders();
    }
?>