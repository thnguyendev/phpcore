<?php
	namespace phpcore\core;

	use Exception;
	
	abstract class Controller {
		protected $Request;
		protected $ViewFolder;

		public function __construct(&$request) {
			try {
				$this->Request = $request;
				$this->ViewFolder = DEFAULT_VIEWS_FOLDER;
				$this->fixViewFolder();
			}
			catch (Exception $e) {
				throw $e;
			}
		}

		public function fixViewFolder() {
			try {
				$this->ViewFolder = preg_replace("/\//", "\\", $this->ViewFolder);
				if (substr($this->ViewFolder, -1) != "\\")
					$this->ViewFolder = $this->ViewFolder . "\\";
			}
			catch (Exception $e) {
				throw $e;
			}
		}

		public function view($view) {
			try {
				require_once($this->ViewFolder . $view . ".php");
			}
			catch (Exception $e) {
				throw $e;
			}
		}

		public abstract function process();
	}
?>