<?php
	class HttpCodes {
		public static function Ok() {
			header("HTTP/1.1 200 OK");
			header("Content-Type: text/plain");
		}

		public static function Unauthorized() {
			header("HTTP/1.1 401 Unauthorized");
			header("Content-Type: text/plain");
		}

		public static function Forbidden() {
			header("HTTP/1.1 403 Forbidden");
			header("Content-Type: text/plain");
		}

		public static function NotFound() {
			header("HTTP/1.1 404 Not Found");
			header("Content-Type: text/plain");
		}

		public static function MethodNotAllowed() {
			header("HTTP/1.1 405 Method Not Allowed");
			header("Content-Type: text/plain");
		}

		public static function InternalServerError() {
			header("HTTP/1.1 500 Internal Server Error");
			header("Content-Type: text/plain");
		}
	}
?>