<?php
namespace App;

use PHPCore\App;

class Bootstrap extends App
{
    public function initialize()
    {
        $this->setRouting(new Route());
        $this->allowCors();
    }

    public function process()
    {
        $this->routing->initialize();
        $route = $this->routing->getRoute($this->request->getMethod(), $this->request->getUri()->getPath());
        // Middleware before controller handle request such as Authorization can apply here
        $this->mapController($route);
        // Middleware after controller handle request can be here
    }
}
?>
