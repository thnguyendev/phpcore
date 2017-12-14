<?php
	class Module {
		public $Folder;
		public $Modules;
		
		public function __construct($folder, $modules) {
			try {
				$this->Folder = $folder;
				$this->Modules = $modules;
			}
			catch (Exception $e) {
				throw $e;
			}
		}
		
		public function GetModule($name) {
			try {
				if (isset($this->Modules[$name])) {
					return $this->Folder . $this->Modules[$name]['file'];
				}
				else {
					return null;
				}
			}
			catch (Exception $e) {
				throw $e;
			}
		}
		
		public function CheckModule($name) {
			try {
				if (isset($this->Modules[$name])) {
					return file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $this->GetModule($name));
				}
				else {
					return false;
				}
			}
			catch (Exception $e) {
				throw $e;
			}
		}
	}
?>