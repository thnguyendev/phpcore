<?php
namespace PHPCore;

use Psr\Http\Message\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class App
{
    protected $container;
    protected $request;
    protected $response;
    protected $routing;
    protected $allowedOrigins;
    protected $allowedMethods;
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
        $this->checkMethod();
    }

    public static function getAppFolder()
    {
        return static::$appFolder;
    }

    protected function setRouting(AppRoute $routing)
    {
        $this->routing = $routing;
    }

    protected function mapController($route, $bucket = null)
    {
        if (!is_array($route))
            throw new \InvalidArgumentException("Route must be an array", 500);
        if (!isset($route[RouteProperties::Controller]) || !class_exists($route[RouteProperties::Controller]))
            throw new NotFoundException("Controller not found", 404);
        if (!isset($route[RouteProperties::Action]))
            throw new NotFoundException("Action not found", 404);
        $reflection = new \ReflectionClass($route[RouteProperties::Controller]);
        if (!$reflection->hasMethod($route[RouteProperties::Action]))
            throw new NotFoundException("Action not found", 404);
        $this->container = $this->container->withSingleton($route[RouteProperties::Controller], $route[RouteProperties::Controller]);
        $controller = $this->container->get($route[RouteProperties::Controller]);
        if (!$controller instanceof Controller)
            throw new \Exception("{$route[RouteProperties::Controller]} is not a controller", 500);
        $controller = $controller
            ->withRequest($this->request)
            ->withResponse($this->response);
        if (isset($route[RouteProperties::View]))
            $controller = $controller->withView($route[RouteProperties::View]);
        if (isset($bucket))
        {
            if (!is_array($bucket))
                $bucket = [$bucket];
            $controller = $controller->withBucket($bucket);
        }
        $reflection->getMethod($route[RouteProperties::Action])->invokeArgs($controller, $route[RouteProperties::Parameters]);
        $controller->applyResponse();
    }

    protected function useHttps()
    {
        $uri = $this->request->getUri();
        if ($uri->getScheme() === "http")
        {
            $uri = $uri->withScheme("https");
            header(Initialization::getProtocol()." 301 ".Response::$defaultReasonPhrase[301], true);
            header("Location: ".$uri->__toString());
            exit;
        }
    }

    public function processRequest()
    {
        $method = $this->request->getMethod();
        if ($method === HttpMethods::Options)
            $this->checkCors();
        else
            $this->process();
    }

    protected function allowCors
    (
        $origins = "*",
        $methods = 
        [
            HttpMethods::Get,
            HttpMethods::Post,
            HttpMethods::Put,
            HttpMethods::Patch,
            HttpMethods::Delete,
        ]
    )
    {
        if (is_string($origins))
            $origins = [$origins];
        if (is_string($methods))
            $methods = [$methods];
        if (!is_array($origins) || !is_array($methods))
            throw new \InvalidArgumentException("Origins and methods must be strings or arrays");
        $this->allowedOrigins = $origins;
        $this->allowedMethods = $methods;
    }

    protected function checkCors()
    {
        $allowed = true;
        $method = $this->request->getHeader("Access-Control-Request-Method");
        if (count($method) > 0)
        {
            if (!in_array($method[0], $this->allowedMethods))
                $allowed = false;
        }
        $origin = $this->request->getHeader("Origin");
        if (count($origin) > 0)
        {
            $parts = parse_url($origin[0]);
            $ori = (isset($parts["scheme"]) ? $parts["scheme"] : "")."://"
                .(isset($parts["host"]) ? $parts["host"] : "").(isset($parts["port"]) ? ":".$parts["scheme"] : "");
            $i = 0;
            $count = count($this->allowedOrigins);
            while ($allowed && $i < $count)
            {
                if ($this->allowedOrigins[$i] === "*")
                    break;
                $parts = parse_url($this->allowedOrigins[$i]);
                $url = (isset($parts["scheme"]) ? $parts["scheme"] : "")."://"
                    .(isset($parts["host"]) ? $parts["host"] : "").(isset($parts["port"]) ? ":".$parts["scheme"] : "");
                if ($ori === $url)
                    break;
                else
                    $i++;
            }
            if ($i === $count)
                $allowed = false;
            if ($allowed)
            {
                header("Access-Control-Allow-Origin: {$origin[0]}");
                header("Access-Control-Allow-Methods: ".join(", ", $this->allowedMethods));
                header(Initialization::getProtocol()." 204 ".Response::$defaultReasonPhrase[204], true);
                exit;
            }
            else
            {
                header(Initialization::getProtocol()." 403 ".Response::$defaultReasonPhrase[403], true);
                exit;
            }
        }
    }

    protected function checkMethod()
    {
        $supportedMethods = 
        [
            HttpMethods::Delete,
            HttpMethods::Get,
            HttpMethods::Options,
            HttpMethods::Patch,
            HttpMethods::Post,
            HttpMethods::Put,
        ];
        $method = "";
        if (isset($this->request))
            $method = $this->request->getMethod();
        if (!in_array($method, $supportedMethods))
        {
            header(Initialization::getProtocol()." 405 ".Response::$defaultReasonPhrase[405], true);
            exit;
        }
    }
}
?>