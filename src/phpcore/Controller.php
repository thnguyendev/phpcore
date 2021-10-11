<?php
	namespace phpcore;

	use Exception;
	
	class Controller implements ControllerInterface {
		private AppInterface $app;

		public function view() {
			$routeService = $this->app->getService("phpcore\\RouteService");
			if (!isset($routeService)) {
				throw new Exception("Route service not found", HttpCodes::internalServerError);
			}
			$route = $routeService->getRoute();
			if (isset($route[RouteDefine::view])) {
				$view = str_replace("/\\/", "/", $route[RouteDefine::view]);
				if (file_exists($view))
					require_once($view);
				else
					throw new Exception(sprintf("View %s not found", $view), HttpCodes::notFound);
			}
		}

		public function process() {
			$this->view();
		}

		public function checkMethod() {
			$requestService = $this->app->getService("phpcore\\RequestService");
			if (!isset($requestService)) {
				throw new Exception("Request service not found", HttpCodes::internalServerError);
			}
			$method = $requestService->getRequest()["Method"];
			if (preg_match("/^OPTIONS$/", $method)) {
				if ($this->app->allowCors()) {
					header(sprintf("Access-Control-Allow-Origin: %s", $this->app->getAllowedOrigins()));
					header(sprintf("Access-Control-Allow-Methods: %s", $this->app->getAllowedMethods()));
					header(sprintf("Access-Control-Allow-Headers: %s", $this->app->getAllowedHeaders()));
					HttpCodes::ok();
				}
				else {
					HttpCodes::forbidden();
				}
			}
			else {
				$this->process();
			}
		}

		public function getApp() {
			return $this->app;
		}

		public function setApp($app) {
			$this->app = $app;
		}
	}
?>
