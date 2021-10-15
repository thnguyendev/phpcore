<?php
    namespace phpcore;

    abstract class App
    {
        protected $services;
        protected $request;
        protected $response;

        abstract public function initialize();
        abstract public function process();
    }
?>