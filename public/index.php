<?php
// PHPWebCore
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


use PHPWebCore\ErrorService;
use PHPWebCore\ErrorServiceInterface;
use App\Bootstrap;

$app = null;
try
{
	require_once(dirname(dirname(__FILE__))."/vendor/autoload.php");
	$app = new Bootstrap();
	$app->initialize();
	$app->process();
}
catch (Throwable $e)
{
	if (isset($app) && $app->getContainer()->has(ErrorServiceInterface::class))
		$errorService = $app->getContainer()->get(ErrorServiceInterface::class);
	else
		$errorService = new ErrorService();
	$errorService->process($e);
}
exit;
?>
