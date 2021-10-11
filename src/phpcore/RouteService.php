<?php
	namespace phpcore;

	use Exception;

	class RouteService implements RouteServiceInterface {
		private $routes;
		private $path;
		private $route;
		private AppInterface $app;

		public function __construct(AppInterface $app) {
			$this->app = $app;
		}

		public function getRoutes() {
			return $this->routes;
		}
		
		public function setRoutes($routes) {
			$this->routes = $routes;
		}

		public function getRoute() {
			return $this->route;
		}

		public function getPath() {
			return $this->path;
		}

		public function mapRoute() {
			if (!isset($this->app)) {
				throw new Exception("App is null in route service", HttpCodes::internalServerError);
			}
			$requestService = $this->app->getService("phpcore\\RequestService");
			if (!isset($requestService)) {
				throw new Exception("Request service not found", HttpCodes::internalServerError);
			}
			$segments = $requestService->getSegments();
			$segmentsLength = count($segments);
			$routes = $this->routes;
			$index = 0;
			$route = null;
			if (isset($routes)) {
				if ($segmentsLength === 0)
					$route = $routes[null];
				else if ($index >= 0 && $index < $segmentsLength && isset($routes[$segments[$index]]))
					$route = $routes[$segments[$index]];
			}
			while (isset($route)) {
				$this->route = $route;
				$index++;
				if (isset($route[RouteDefine::children]) && ($index >= 0 && $index < $segmentsLength) && isset($route[RouteDefine::children][$segments[$index]]))
					$route = $route[RouteDefine::children][$segments[$index]];
				else 
					$route = null;
			}
			$this->path = sprintf("/%s", join("/", array_slice($segments, 0, $index)));
			$requestService->setQuerry(array_slice($segments, $index));
			if (!isset($this->route)) {
				throw new Exception("Route not found", HttpCodes::notFound);
			}
		}

		public function mapController() {
			if (isset($this->route)) {
				if (isset($this->route[RouteDefine::controller])) {
					$controllerClass = preg_replace("/\//", "\\", $this->route[RouteDefine::controller]); 
				}
				else {
					throw new Exception(sprintf("Controller of %s not found", $this->path), HttpCodes::notFound);
				}
				if (class_exists($controllerClass)) {
					$controller = new $controllerClass();
					$controller->setApp($this->app);
					$controller->checkMethod();
				}
				else {
					throw new Exception(sprintf("Controller %s not found", $controllerClass), HttpCodes::notFound);
				}
			}
			else {
				throw new Exception("Route not found", HttpCodes::notFound);
			}
		}
	}
?>
