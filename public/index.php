<?php
// PHPCore
// Author: Hung Thanh Nguyen

// Web server configurations
// Apache: modify .htaccess as following
//   RewriteEngine On
//   RewriteCond %{REQUEST_FILENAME} !-f
//   RewriteCond %{REQUEST_FILENAME} !-d
//   RewriteRule ^(.*)$ index.php/$1 [QSA,NC,L]
// Nginx: modify nginx.conf as following:
//   location / {
//		rewrite ^/(.*)/$ /$1 permanent;
//		try_files $uri $uri/ /index.php$is_args$args;
//	}

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
use PHPCore\ErrorService;
use PHPCore\ErrorServiceInterface;
use PHPCore\Container;
use PHPCore\Initialization;
use App\Bootstrap;

$container = null;
try
{
	require_once(sprintf("%s/../vendor/autoload.php", __DIR__));
	$container = new Container();
	$uriFactory = new UriFactory();
	$streamFactory = new StreamFactory();
	$requestFactory = new ServerRequestFactory();
	$responseFactory = new ResponseFactory();
	$container = $container
		->withSingleton(RequestFactoryInterface::class, RequestFactory::class)
		->withSingleton(ResponseFactoryInterface::class, $responseFactory)
		->withSingleton(ServerRequestFactoryInterface::class, $requestFactory)
		->withSingleton(StreamFactoryInterface::class, $streamFactory)
		->withSingleton(UploadedFileFactoryInterface::class, UploadedFileFactory::class)
		->withSingleton(UriFactoryInterface::class, $uriFactory)
		->withSingleton(ErrorServiceInterface::class, ErrorService::class);
	$request = $requestFactory->createServerRequest(Initialization::getMethod(), "", Initialization::getServerParams())
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
		$request = $request->withHeader($name, $value);
	$response = $responseFactory->createResponse();
	$appInstance = new Bootstrap($container, $request, $response);
	$appInstance->initialize();
	$appInstance->process();
}
catch (Throwable $e)
{
	if ($container->has(ErrorServiceInterface::class))
		$errorService = $container->get(ErrorServiceInterface::class);
	else
		$errorService = new ErrorService();
	$errorService->process($e);
}
?>
