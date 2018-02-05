<?php
	namespace phpcore\core;

	use Exception;

	abstract class ApiController extends Controller {

		public function process() {
			try {
				if (preg_match("/^GET$/", $this->Request->Method)) {
					$this->get();
				}
				else if (preg_match("/^POST$/", $this->Request->Method)) {
					$this->post();
				}
				else if (preg_match("/^PUT$/", $this->Request->Method)) {
					$this->put();
				}
				else if (preg_match("/^DELETE$/", $this->Request->Method)) {
					$this->delete();
				}
				else if (preg_match("/^PATCH$/", $this->Request->Method)) {
					$this->patch();
				}
				else if (preg_match("/^OPTIONS$/", $this->Request->Method)) {
					$this->options();
				}
			}
			catch (Exception $e) {
				throw $e;
			}
		}

		public abstract function get();
		public abstract function post();
		public abstract function put();
		public abstract function delete();
		public abstract function patch();
		public abstract function options();
	}
?>
