<?php
	namespace phpcore;

	use Exception;
	
	class RequestService implements RequestServiceInterface {
		private $body;
		private $https;
		private $segments = array();
		private $querry = array();
		private $header = array();
		private $request = array();
		private $server = array();
		private AppInterface $app;
		
		public function __construct(AppInterface $app) {
			$this->app = $app;
			
			if (isset($_REQUEST["q"])) {
				$this->segments = explode("/", ltrim(rtrim($_REQUEST["q"], "/"), "/"));
			}

			$this->body = file_get_contents("php://input");

			foreach ($_SERVER as $key => $value) {
				if (preg_match("/^HTTP_/", $key)) {
					$key = str_replace(" ", "-", ucwords(strtolower(str_replace("_", " ", substr($key, 5))))); 
					$this->header[$key] = $value;
				} else if ($key === "CONTENT_TYPE") { 
					$this->header["Content-Type"] = $value; 
				} else if ($key === "CONTENT_LENGTH") { 
					$this->header["Content-Length"] = $value;
				} else if(preg_match("/^REQUEST_/", $key)) {
					$key = str_replace(" ", "-", ucwords(strtolower(str_replace("_", " ", substr($key, 8))))); 
					$this->request[$key] = $value;
				} else if(preg_match("/^SERVER_/", $key)) {
					$key = str_replace(" ", "-", ucwords(strtolower(str_replace("_", " ", substr($key, 7))))); 
					$this->server[$key] = $value;
				} else if ($key === "HTTPS") {
					$this->https = $value;
				}
			}
		}

		public function getHeader() {
			return $this->header;
		}

		public function getRequest() {
			return $this->request;
		}

		public function getServer() {
			return $this->server;
		}

		public function getHttps() {
			return $this->https;
		}

		public function getQuerry() {
			return $this->querry;
		}

		public function getBody() {
			return $this->body;
		}

		public function getSegments() {
			return $this->segments;
		}

		public function setQuerry($querry) {
			$this->querry = $querry;
		}

		public function redirectToHttps() {
			if ((!isset($this->https) || strtolower($this->https) !== "on") && 
			(!isset($this->request["Scheme"]) || strtolower($this->request["Scheme"]) !== "https")) {
				HttpCodes::movePermanently();
				$uri = $this->request["Uri"];
				if(preg_match("{^/(.*)/$}", $uri))
					$uri = sprintf("Location: %s", preg_replace("{/$}", "", $uri));
    			header(sprintf("Location: https://%s%s", $this->header["Host"], $uri));
    			exit();
			}
		}

		public function redirectToNoTrailingSlash() {
			if(preg_match("{^/(.*)/$}", $this->request["Uri"])) {
				HttpCodes::found();
				header (sprintf("Location: %s", preg_replace("{/$}", "", $this->request["Uri"])));
				exit();
			}
		}
	}
?>