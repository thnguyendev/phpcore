<?php
  abstract class ApiController extends Controller {
		protected $Views;
		protected $Request;

		public function __construct(&$request, &$views) {
			parent::__construct($request, $view);
			try {
				header('Access-Control-Allow-Origin: ' . $this->Request->Origin);
				header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
				header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
			}
			catch (Exception $e) {
				throw $e;
			}
		}
	}
?>