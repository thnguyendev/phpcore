<?php
namespace PHPWebCore;

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

    protected function invokeAction($route, $bucket = null)
    {
        if (!is_array($route))
            throw new \InvalidArgumentException("Route must be an array", 500);
        if (!isset($route[RouteProperty::Controller]) || !class_exists($route[RouteProperty::Controller]))
            throw new NotFoundException("Controller not found", 404);
        if (!isset($route[RouteProperty::Action]))
            throw new NotFoundException("Action not found", 404);
        $reflection = new \ReflectionClass($route[RouteProperty::Controller]);
        if (!$reflection->hasMethod($route[RouteProperty::Action]))
            throw new NotFoundException("Action not found", 404);
        $this->container = $this->container->withSingleton($route[RouteProperty::Controller], $route[RouteProperty::Controller]);
        $controller = $this->container->get($route[RouteProperty::Controller]);
        if (!$controller instanceof Controller)
            throw new \Exception("{$route[RouteProperty::Controller]} is not a controller", 500);
        $controller = $controller
            ->withRequest($this->request)
            ->withResponse($this->response);
        if (isset($route[RouteProperty::View]))
            $controller = $controller->withView($route[RouteProperty::View]);
        if (isset($bucket))
        {
            if (!is_array($bucket))
                $bucket = [$bucket];
            $controller = $controller->withBucket($bucket);
        }
        $method = $reflection->getMethod($route[RouteProperty::Action]);
        $params = $method->getParameters();
        $values = $route[RouteProperty::Parameters];
        $args = [];
        $i = 0;
        foreach($params as $param)
        {
            $name = $param->getName();
            if (isset($values[$name]))
                $args[$name] = $values[$name];
            else if (isset($values[$i]))
            {
                $args[$name] = $values[$i];
                $i++;
            }
        }
        $method->invokeArgs($controller, $args);
        $controller->applyResponse();
    }

    protected function useHttps()
    {
        $uri = $this->request->getUri();
        if ($uri->getScheme() === "http")
        {
            $uri = $uri->withScheme("https");
            header(Initialization::getProtocol()." 301 ".Response::ReasonPhrase[301], true);
            header("Location: ".$uri->__toString());
            exit;
        }
    }

    public function processRequest()
    {
        $method = $this->request->getMethod();
        if ($method === HttpMethod::Options)
            $this->checkCors();
        else
            $this->process();
    }

    protected function allowCors
    (
        $origins = "*",
        $methods = 
        [
            HttpMethod::Get,
            HttpMethod::Post,
            HttpMethod::Put,
            HttpMethod::Patch,
            HttpMethod::Delete,
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
        $method = $this->request->getHeader(HttpHeader::AccessControlRequestMethod);
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
                header(HttpHeader::AccessControlAllowOrigin.": {$origin[0]}");
                header(HttpHeader::AccessControlAllowMethods.": ".join(", ", $this->allowedMethods));
                header(Initialization::getProtocol()." 204 ".Response::ReasonPhrase[204], true);
                exit;
            }
            else
            {
                header(Initialization::getProtocol()." 403 ".Response::ReasonPhrase[403], true);
                exit;
            }
        }
    }

    protected function checkMethod()
    {
        $supportedMethods = 
        [
            HttpMethod::Delete,
            HttpMethod::Get,
            HttpMethod::Options,
            HttpMethod::Patch,
            HttpMethod::Post,
            HttpMethod::Put,
        ];
        $method = "";
        if (isset($this->request))
            $method = $this->request->getMethod();
        if (!in_array($method, $supportedMethods))
        {
            header(Initialization::getProtocol()." 405 ".Response::ReasonPhrase[405], true);
            exit;
        }
    }
}
?>