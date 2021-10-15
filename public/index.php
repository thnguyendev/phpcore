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
use App\Bootstrap;

try
{
	require_once(sprintf("%s/../vendor/autoload.php", __DIR__));

	$requestFactory = new ServerRequestFactory();
	$request = $requestFactory->createServerRequest($_SERVER["REQUEST_METHOD"], null);
	$responseFactory = new ResponseFactory();
	$response = $responseFactory->createResponse();
	$container = (new Container())
		->withSingleton(RequestFactoryInterface::class, RequestFactory::class)
		->withSingleton(ResponseFactoryInterface::class, $responseFactory)
		->withSingleton(ServerRequestFactoryInterface::class, $requestFactory)
		->withSingleton(StreamFactoryInterface::class, StreamFactory::class)
		->withSingleton(UploadedFileFactoryInterface::class, UploadedFileFactory::class)
		->withSingleton(UriFactoryInterface::class, UriFactory::class)
		->withSingleton(ErrorServiceInterface::class, ErrorService::class);

	$appInstance = new Bootstrap($container, $request, $response);
	$appInstance->initialize();
	$appInstance->process();
}
catch (Throwable $e)
{
	if ($container->has(ErrorServiceInterface::class))
		$errorService = $container->get(ErrorServiceInterface::class);
	else
		$errorService = new ErrorService(new ResponseFactory());
	$errorService->process($e);
}
?>
