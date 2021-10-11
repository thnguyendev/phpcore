<?php
    namespace phpcore;

    interface RouteServiceInterface {
        public function getRoutes();
        public function setRoutes($routes);
        public function getRoute();
        public function getPath();
        public function mapRoute();
        public function mapController();
    }
?>