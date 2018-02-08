<?php
    namespace phpcore;

    use Exception;
    use phpcore\core\App;
    use phpcore\core\Route;

    class Startup extends App {

        // declare api controllers
        public $ApiRoutes = array();

        // declare web controllers
        public $WebRoutes = array();

        public function __construct() {
            try {
                parent::__construct();
                $this->Route->setApiRoutes($this->ApiRoutes);
                $this->Route->setWebRoutes($this->WebRoutes);
            }
            catch (Exception $e) {
				throw $e;
			}
        }

        public function process() {
            try {
                if ($this->Request->IsApi) {
                    $this->useCors("*");
                }

                $this->useMvc();
            }
            catch (Exception $e) {
				throw $e;
			}
        }
    }
?>
