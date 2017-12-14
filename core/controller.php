<?php
  abstract class Controller {
		protected $Views;
		protected $Request;

		public function __construct(&$request, &$views) {
			try {
				$this->Request = $request;
				$this->Views = $views;
			}
			catch (Exception $e) {
				throw $e;
			}
		}

		public abstract function Process();
	}
?>