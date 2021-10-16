<?php
namespace PHPCore;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class App
{
    protected $container;
    protected $request;
    protected $response;
    protected $route;

    abstract public function initialize();
    abstract public function process();

    public function __construct
    (
        ContainerInterface $container,
        ServerRequestInterface $request,
        ResponseInterface $response
    )
    {
        $this->container = $container;
        $this->request = $request;
        $this->response = $response;
    }

    protected function useRoute(AppRoute $route)
    {
        $this->route = $route;
    }
}
?>