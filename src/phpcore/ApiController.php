<?php
	namespace Phpcore;

	use Exception;

	class ApiController implements ApiControllerInterface {
		private AppInterface $app;

		public function checkMethod() {
			$requestService = $this->app->getService("phpcore\\core\\RequestService");
			if (!isset($requestService)) {
				throw new Exception("Request service not found", HttpCodes::internalServerError);
			}
			$method = $requestService->getRequest()["Method"];
			if (preg_match("/^GET$/", $method)) {
				$this->get();
			}
			else if (preg_match("/^POST$/", $method)) {
				$this->post();
			}
			else if (preg_match("/^PUT$/", $method)) {
				$this->put();
			}
			else if (preg_match("/^DELETE$/", $method)) {
				$this->delete();
			}
			else if (preg_match("/^PATCH$/", $method)) {
				$this->patch();
			}
			else if (preg_match("/^OPTIONS$/", $method)) {
				$this->options();
			}
			else {
				throw new Exception("Unknown method", HttpCodes::methodNotAllowed);
			}
		}
		
		public function get() {
            HttpCodes::methodNotAllowed();
        }

        public function post() {
            HttpCodes::methodNotAllowed();
        }

        public function put() {
            HttpCodes::methodNotAllowed();
        }

        public function delete() {
            HttpCodes::methodNotAllowed();
        }

        public function patch() {
            HttpCodes::methodNotAllowed();
		}
		
		public function options() {
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

		public function getApp() {
			return $this->app;
		}

		public function setApp($app) {
			$this->app = $app;
		}
	}
?>
