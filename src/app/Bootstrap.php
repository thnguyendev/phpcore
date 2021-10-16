<?php
namespace App;

use PHPCore\App;

class Bootstrap extends App
{
    public function initialize()
    {
        $this->setRoute(new Route());
    }

    public function process()
    {
        $this->route->initialize();
        $route = $this->route->getRoute($this->request->getMethod(), $this->request->getUri()->getPath());
        // Middleware before controller handle request such as Authorization can apply here
        $this->getController($route)->processRequest();
        // Middleware after controller handle request can be here
    }
}
?>
