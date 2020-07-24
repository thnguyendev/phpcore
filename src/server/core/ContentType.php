<?php
	namespace phpcore\core;
	
	class ContentType {
		
		public static function applicationJson() {
			header("Content-Type: application/json");
		}

		public static function textCsv() {
            header("Content-Type: text/csv");
        }

        public static function textHtml() {
            header("Content-Type: text/html");
        }

        public static function textXml() {
            header("Content-Type: text/xml");
        }
	}
?>