<?php
	namespace phpcore\core;

	use Exception;
	
	class Request {
		public $IsApi = false;
		public $Origin;
		public $Method;
		public $Controller;
		public $Arguments = array();
		public $Headers = array();
		
		public function __construct() {
			try {
				// Requests from the same server don't have a HTTP_ORIGIN header
				if (array_key_exists('HTTP_ORIGIN', $_SERVER))
					$this->Origin = $_SERVER['HTTP_ORIGIN'];
				else
					$this->Origin = $_SERVER['SERVER_NAME'];
				$this->Method = $_SERVER['REQUEST_METHOD'];
				if (isset($_REQUEST['q'])) {
					$this->Arguments = explode('/', ltrim(rtrim($_REQUEST['q'], '/'), '/'));
				}
				$this->Controller = array_shift($this->Arguments);
				if (preg_match('/^api$/i', $this->Controller)) {
					$this->IsApi = true;
					$this->Controller = array_shift($this->Arguments);
				}
				$this->Headers = $this->GetRequestHeaders();
			}
			catch (Exception $e) {
				throw $e;
			}
		}

		public function getRequestHeaders() {
			try {
				if (!function_exists('apache_request_headers')) {  
					foreach ($_SERVER as $key => $value) {
						if (preg_match('/^HTTP_/', $key)) {
							$key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5))))); 
							$headers[$key] = $value;
						} else if ($key == "CONTENT_TYPE") { 
							$headers["Content-Type"] = $value; 
						} else if ($key == "CONTENT_LENGTH") { 
							$headers["Content-Length"] = $value; 
						}
					}
				}
				else {
					$headers = apache_request_headers();
				}
				return $headers;
			}
			catch (Exception $e) {
				throw $e;
			}
		}
	}
?>