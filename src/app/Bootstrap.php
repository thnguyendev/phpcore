<?php
namespace App;

use PHPCore\App;

class Bootstrap extends App
{
    public function initialize()
    {
        $this->useRoute(new Route());
    }

    public function process()
    {
        $this->route->initialize();
        var_dump($this->route->getRoute($this->request->getMethod(), $this->request->getUri()->getPath()));
    }
}
?>
