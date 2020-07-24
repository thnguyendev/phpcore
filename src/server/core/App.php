<?php
    namespace phpcore\core;

    use Exception;

    class App implements AppInterface{
        private $services = array();
        private $allowedOrigins;
		private $allowedMethods;
		private $allowedHeaders;

        public function __construct() {
            $this->addService(new RequestService($this));
            $this->addService(new RouteService($this));
            $this->getService("phpcore\\core\\RequestService")->redirectToNoTrailingSlash();
        }

        public function process() {
            try {
                $this->getService("phpcore\\core\\RouteService")->mapController();
            }
            catch(Exception $exception) {
                throw $exception;
            }
        }

        public function addService(object $service) {
            array_push($this->services, $service);
        }

        public function getService(string $className) {
            $service = null;
            $length = count($this->services);
            $i = 0;
            while ($i < $length && get_class($this->services[$i]) != $className) {
                $i++;
            }
            if ($i < $length)
                $service = $this->services[$i];
            return $service;
        }

        public function enableCors($origins = "", $methods = "", $headers = "") {
            $requestService = $this->getService("phpcore\\core\\RequestService");
			if (!isset($requestService)) {
				throw new Exception("Request service not found", HttpCodes::internalServerError);
			}
            if ($origins === "" || $origins === "*")
                if (isset($requestService->getHeader()["Origin"])) {
                    $this->allowedOrigins = $requestService->getHeader()["Origin"];
                }
			else
				$this->allowedOrigins = $origins;
			if ($methods === "" || $methods === "*")
				$this->allowedMethods = "GET, POST, PUT, DELETE, PATCH, OPTIONS";
			else
				$this->allowedMethods = $methods;
			if ($headers === "" || $headers === "*")
				$this->allowedHeaders = "Origin, X-Requested-With, Content-Type, Accept, Authorization";
			else
				$this->allowedHeaders = $headers;
        }
        
        public function allowCors() {
			$requestService = $this->getService("phpcore\\core\\RequestService");
			if (!isset($requestService)) {
				throw new Exception("Request service not found", HttpCodes::internalServerError);
			}
            $allow = true;
			if (isset($requestService->getHeader()["Origin"])) {
                $origin = $requestService->getHeader()["Origin"];
				$origins = explode(",", $this->allowedOrigins);
				$i = 0;
                $count = count($origins);
				while($i < $count && ltrim(rtrim($origins[$i])) !== $origin)
					$i++;
				if ($i === $count)
					$allow = false;
			}
			return $allow;
        }
        
        public function getAllowedOrigins() {
            return $this->allowedOrigins;
        }

        public function getAllowedMethods() {
            return $this->allowedMethods;
        }

        public function getAllowedHeaders() {
            return $this->allowedHeaders;
        }
    }
?>
