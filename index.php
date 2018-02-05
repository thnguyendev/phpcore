<?php
	// PHP Core
	// Author: Hung Thanh Nguyen

	// Web server configurations
	// Apache: modify .htaccess as following
	//   RewriteEngine On
	//   RewriteCond %{REQUEST_FILENAME} !-f
	//   RewriteCond %{REQUEST_FILENAME} !-d
	//   RewriteRule ^(.*)$ index.php?q=$1 [QSA,NC,L]
	// Nginx: modify nginx.conf as following:
	//   location / {
	//   	index  index.html index.htm index.php;
	//   	if (!-e $request_filename) {
	//   		rewrite ^(.*)$ /index.php?q=$1;
	//   	}
	//   }
	
	$App = null;

	try {
		require_once "vendor/autoload.php";

		session_start();

		require_once("src/server/config.php");

		$AppName = STARTUP;
		$App = new $AppName();
		$App->process();
	}
	catch (Exception $e) {
		$App->ErrorHandler->process($e);
	}
?>
