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

	try {
		session_start();
		
		require_once("core/module.php");
		require_once("core/controllermodule.php");
		require_once("core/request.php");
		require_once("core/controller.php");
		require_once("core/apicontroller.php");
		require_once("core/httpcodes.php");
	
		require_once("modules/api.php");
		require_once("modules/controller.php");
		require_once("modules/model.php");
		require_once("modules/view.php");
		require_once("modules/package.php");
	
		$Api = new ControllerModule(ApiFolder, $ApiList);
		$Controllers = new ControllerModule(ControllerFolder, $ControllerList);
		$Models = new Module(ModelFolder, $ModelList);
		$Views = new Module(ViewFolder, $ViewList);
		$Packages = new Module(PackageFolder, $PackageList);
		
		$Request = new Request();
	
		if ($Request->IsApi) {
			if (!$Api->CheckModule($Request->Controller)) {
				HttpCodes::NotFound();
			}
			else {
				require_once($Api->GetModule($Request->Controller));
				$ControllerName = $Api->GetClass($Request->Controller);
				$Controller = new $ControllerName($Request, $Views);
				$Controller->Process();
			}
		}
		else {
			if (!$Controllers->CheckModule($Request->Controller)) {
				HttpCodes::NotFound();
			}
			else {
				require_once($Controllers->GetModule($Request->Controller));
				$ControllerName = $Controllers->GetClass($Request->Controller);
				$Controller = new $ControllerName($Request, $Views);
				$Controller->Process();
			}
		}
	}
	catch (Exception $e) {
		HttpCodes::InternalServerError();
	}
?>
