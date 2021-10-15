<?php
	// PHP Core
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

	use app\Bootstrap;
	use phpcore\ErrorService;
	use phpcore\ErrorServiceInterface;

	try
	{
		require_once(sprintf("%s/../vendor/autoload.php", __DIR__));

		$appInstance = new Bootstrap();
		$appInstance->initialize();
		$appInstance->process();
	}
	catch (Throwable $e)
	{
		$errorService = new ErrorService();
		$errorService->process($e);
	}
?>
