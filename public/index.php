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
	//   	index  index.html index.htm index.php;
	//   	try_files $uri $uri/ /index.php$is_args$args;
	//   	}
	//   }

	use app\Startup;

	try {
		require_once(sprintf("%s/../vendor/autoload.php", __DIR__));

		$appInstance = new Startup();
		$appInstance->process();
	}
	catch (Exception $exception) {
		$errorName = ERROR_CLASS;
		$errorService = new $errorName();
		$errorService->process($exception);
	}
?>
