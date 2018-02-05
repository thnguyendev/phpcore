<?php
    namespace phpcore\core;

    use Exception;

    abstract class App {
        public $Request;
        public $Route;
        public $ErrorHandler;

        public function __construct() {
            try {
                $this->Request = new Request();
                $this->Route = new Route();
                $this->setErrorHandler(new DefaultErrorHandler());
            }
            catch (Exception $e) {
				throw $e;
			}
        }

        public function setErrorHandler(IErrorHandler $errorHandler) {
            try {
                $this->ErrorHandler = $errorHandler;
            }
            catch (Exception $e) {
				throw $e;
			}
        }

        public function useMvc() {
            try {
                $ControllerClass = $this->Route->getController($this->Request->Controller, $this->Request->IsApi);
                $Controller = new $ControllerClass($this->Request);
                $Controller->process();
            }
            catch (Exception $e) {
				throw $e;
			}
        }

        public function useCors(string $origin) {
            try {
                if ($origin == "" || $origin == "*")
                    $origin = $this->Request->Origin;
                header('Access-Control-Allow-Origin: ' . $origin);
                header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
                header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
            }
            catch (Exception $e) {
				throw $e;
			}
        }

        abstract public function process();
    }
?>
