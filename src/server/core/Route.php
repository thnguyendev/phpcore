<?php
	namespace phpcore\core;

	use Exception;

	class Route {
		public $Api;
		public $Web;
		
		public function setApiRoutes($routes) {
			try {
				$this->Api = $routes;
			}
			catch (Exception $e) {
				throw $e;
			}
		}

		public function setWebRoutes($routes) {
			try {
				$this->Web = $routes;
			}
			catch (Exception $e) {
				throw $e;
			}
		}
		
		public function getController($name, $isApi) {
			try {
				if ($isApi) {
					$routes = $this->Api;
				}
				else {
					$routes = $this->Web;
				}
				if (isset($routes["namespace"])) {
					$namespace = $routes["namespace"];
				}
				if (isset($routes[$name])) {
					$controller = $routes[$name];
					if (isset($controller["namespace"])) {
						$namespace = $controller["namespace"];
					}
					$namespace = preg_replace("/\//", "\\", $namespace);
					if (substr($namespace, -1) != "\\")
						$namespace = $namespace . "\\";
					return $namespace . $controller["controller"];
				}
				else {
					throw new Exception("Controller not found");
				}
			}
			catch (Exception $e) {
				throw $e;
			}
		}
	}
?>