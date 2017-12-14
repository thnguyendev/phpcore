<?php
	class ControllerModule extends Module {
		public function GetClass($name) {
			try {
				if (isset($this->Modules[$name])) {
					return $this->Modules[$name]['class'];
				}
				else {
					throw new Exception("Class name not found");
				}
			}
			catch (Exception $e) {
				throw $e;
			}
		}
	}
?>