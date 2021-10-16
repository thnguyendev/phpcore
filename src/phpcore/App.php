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
    private static $appFolder;

    abstract public function initialize();
    abstract public function process();

    public function __construct
    (
        ContainerInterface $container,
        ServerRequestInterface $request,
        ResponseInterface $response,
        string $appFolder,
    )
    {
        $this->container = $container;
        $this->request = $request;
        $this->response = $response;
        static::$appFolder = $appFolder;
    }

    public static function getAppFolder()
    {
        return static::$appFolder;
    }

    protected function setRoute(AppRoute $route)
    {
        $this->route = $route;
    }

    protected function getController($route, $bucket = null)
    {
        if (!is_array($route))
            throw new \InvalidArgumentException("Route must be an array", 500);
        if (!isset($route[AppRoute::RouteController]) || !class_exists($route[AppRoute::RouteController]))
            throw new NotFoundException("Controller not found", 404);
        $controller = new $route[AppRoute::RouteController]();
        if (!$controller instanceof Controller)
            throw new \Exception("{$route[AppRoute::RouteController]} is not a controller", 500);
        $controller = $controller
            ->withRequest($this->request)
            ->withResponse($this->response);
        if (isset($route[AppRoute::RouteParameters]))
            $controller = $controller->withParameters($route[AppRoute::RouteParameters]);
        if (isset($route[AppRoute::RouteView]))
            $controller = $controller->withView($route[AppRoute::RouteView]);
        if (isset($bucket))
        {
            if (!is_array($bucket))
                $bucket = [$bucket];
            $controller = $controller->withBucket($bucket);
        }
        return $controller;
    }
}
?>