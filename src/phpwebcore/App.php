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
    protected $route;
    protected $allowedOrigins;
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

    protected function allowCors($origins = "*")
    {
        if (!is_string($origins) && !is_array($origins))
            throw new \InvalidArgumentException("Origins must be string or arrays");
        $this->allowedOrigins = $origins;
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

    protected function useRouting()
    {
        $this->routing->initialize();
        $this->route = $this->routing->getRoute($this->request->getMethod(), $this->request->getUri()->getPath());
    }

    protected function invokeAction($bucket = null)
    {
        if ($this->request->getMethod() === HttpMethod::Options)
            $this->checkCors($this->route);
        if (!is_array($this->route))
            throw new \InvalidArgumentException("Route must be an array", 500);
        if (!isset($this->route[RouteProperty::Controller]) || !class_exists($this->route[RouteProperty::Controller]))
            throw new NotFoundException("Controller not found", 404);
        if (!isset($this->route[RouteProperty::Action]))
            throw new NotFoundException("Action not found", 404);
        $reflection = new \ReflectionClass($this->route[RouteProperty::Controller]);
        if (!$reflection->hasMethod($this->route[RouteProperty::Action]))
            throw new NotFoundException("Action not found", 404);
        $this->container = $this->container->withSingleton($this->route[RouteProperty::Controller], $this->route[RouteProperty::Controller]);
        $controller = $this->container->get($this->route[RouteProperty::Controller]);
        if (!$controller instanceof Controller)
            throw new \Exception("{$this->route[RouteProperty::Controller]} is not a controller", 500);
        $controller = $controller
            ->withRequest($this->request)
            ->withResponse($this->response);
        if (isset($this->route[RouteProperty::View]))
            $controller = $controller->withView($this->route[RouteProperty::View]);
        if (isset($bucket))
        {
            if (!is_array($bucket))
                $bucket = [$bucket];
            $controller = $controller->withBucket($bucket);
        }
        $method = $reflection->getMethod($this->route[RouteProperty::Action]);
        $params = $method->getParameters();
        $values = $this->route[RouteProperty::Parameters];
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

    protected function checkCors()
    {
        $allowed = false;
        $allowedOrigins = $this->allowedOrigins;
        if (isset($this->route[RouteProperty::AllowedOrigins])
            && (is_string($this->route[RouteProperty::AllowedOrigins]) || is_array($this->route[RouteProperty::AllowedOrigins])))
            $allowedOrigins = $this->route[RouteProperty::AllowedOrigins];
        if ($allowedOrigins === "*")
            $allowed = true;
        $origin = $this->request->getHeaderLine(HttpHeader::Origin);
        if (is_string($allowedOrigins))
        {
            if ($allowedOrigins === $origin)
                $allowed = true;
        }
        else
        {
            $i = 0;
            $count = count($allowedOrigins);
            while (!$allowed && $i < $count)
            {
                if ($allowedOrigins[$i] === $origin)
                    $allowed = true;
                $i++;
            }
        }
        if ($allowed)
        {
            header(HttpHeader::AccessControlAllowOrigin.": {$origin}");
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
?>