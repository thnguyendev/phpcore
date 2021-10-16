<?php
namespace PHPCore;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class App
{
    protected $services;
    protected $request;
    protected $response;
    protected $route;

    abstract public function initialize();
    abstract public function process();

    public function __construct
    (
        ContainerInterface $services,
        ServerRequestInterface $request,
        ResponseInterface $response
    )
    {
        $this->services = $services;
        $this->request = $request;
        $this->response = $response;
    }

    protected function useRoute(AppRoute $route)
    {
        $this->route = $route;
    }
}
?>