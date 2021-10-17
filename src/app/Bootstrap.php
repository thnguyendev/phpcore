<?php
namespace App;

use PHPWebCore\App;

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
        // Middleware before passing request to controller such as Authorization can apply here
        $this->invokeAction($route);
        // Middleware after invoke the action can be here
    }
}
?>
