<?php
namespace PHPWebCore;

use Psr\Http\Message\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\RequestFactory;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactory;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactory;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactory;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactory;
use Psr\Http\Message\UriFactoryInterface;

abstract class App
{
    protected ContainerInterface $container;
    protected ServerRequestInterface $request;
    protected ResponseInterface $response;
    protected AppRoute $routing;
    protected array $route;
    protected string|array $allowedOrigins;
    private static string $appFolder;

    /**
     * Implement all tasks in app
     */
    abstract public function process();

    /**
     * Return an instance with the specified container appended with the given value.
     *
     * @param ContainerInterface $container
     * @return static
     */
    public function withContainer(ContainerInterface $container)
    {
        $clone = clone $this;
        $clone->container = $container;
        return $clone;
    }

    /**
     * Return an instance with the specified request appended with the given value.
     *
     * @param ServerRequestInterface $request
     * @return static
     */
    public function withRequest(ServerRequestInterface $request)
    {
        $clone = clone $this;
        $clone->request = $request;
        return $clone;
    }

    /**
     * Return an instance with the specified response appended with the given value.
     *
     * @param ResponseInterface $response
     * @return static
     */
    public function withResponse(ResponseInterface $response)
    {
        $clone = clone $this;
        $clone->response = $response;
        return $clone;
    }

    /**
     * Return an instance with the specified based folder appended with the given value.
     *
     * @param string $appFolder
     * @return static
     */
    public function withAppFolder(string $appFolder)
    {
        $clone = clone $this;
        static::$appFolder = $appFolder;
        return $clone;
    }

    /**
     * Return the container.
     *
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Return based folder of app.
     *
     * @return string
     */
    public static function getAppFolder()
    {
        return static::$appFolder;
    }

    /**
     * Apply the origins will be allowed in CORS.
     *
     * @param string|string[] $origin
     */
    protected function allowCors($origins = "*")
    {
        if (!is_string($origins) && !is_array($origins))
            throw new \InvalidArgumentException("Origins must be string or arrays");
        $this->allowedOrigins = $origins;
    }

    /**
     * Redirect to HTTPS.
     */
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

    /**
     * Find a route for request.
     *
     * @param AppRoute $routing
     */
    protected function useRouting(AppRoute $routing)
    {
        $this->routing = $routing;
        $this->routing->initialize();
        $this->route = $this->routing->getRoute($this->request->getMethod(), $this->request->getUri()->getPath());
    }

    /**
     * Invoke the action of controller.
     *
     * @param mixed $bucket data pass to the controller
     */
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
            $controller = $controller->withBucket($bucket);
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

    /**
     * Validate HTTP methods from request.
     */
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

    /**
     * Validate the allowed CORS.
     */
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

    /**
     * Initilize the app.
     */
    public function initialize()
    {
        $uriFactory = new UriFactory();
	    $streamFactory = new StreamFactory();
	    $requestFactory = new ServerRequestFactory();
	    $responseFactory = new ResponseFactory();
        $this->container = (new Container())
            ->withSingleton(RequestFactoryInterface::class, RequestFactory::class)
            ->withSingleton(ResponseFactoryInterface::class, $responseFactory)
            ->withSingleton(ServerRequestFactoryInterface::class, $requestFactory)
            ->withSingleton(StreamFactoryInterface::class, $streamFactory)
            ->withSingleton(UploadedFileFactoryInterface::class, UploadedFileFactory::class)
            ->withSingleton(UriFactoryInterface::class, $uriFactory)
            ->withSingleton(ErrorServiceInterface::class, ErrorService::class);
	    $this->request = $requestFactory->createServerRequest(Initialization::getMethod(), "", Initialization::getServerParams())
            ->withProtocolVersion(Initialization::getProtocolVersion())
            ->withQueryParams(Initialization::getQueryParams())
            ->withBody($streamFactory->createStreamFromFile(Initialization::getBody()))
            ->withParsedBody(Initialization::getParsedBody())
            ->withCookieParams(Initialization::getCookies())
            ->withUploadedFiles(Initialization::getUploadedFiles())
            ->withUri($uriFactory->createUri()
                ->withScheme(Initialization::getScheme())
                ->withUserInfo(Initialization::getUser(), Initialization::getPassword())
                ->withHost(Initialization::getHost())
                ->withPort(Initialization::getPort())
                ->withPath(Initialization::getPath())
                ->withQuery(Initialization::getQuery()));
        foreach (Initialization::getHeaders() as $name => $value)
            $this->request = $this->request->withHeader($name, $value);
	    $this->response = $responseFactory->createResponse();
        static::$appFolder = dirname(dirname(__FILE__))."/app";
        $this->checkMethod();
    }
}
?>