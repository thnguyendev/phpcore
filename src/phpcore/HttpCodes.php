<?php
	namespace phpcore;

	use Exception;
	
	class HttpCodes {
		public const unauthorized = 401;
		public const forbidden = 403;
		public const notFound = 404;
		public const methodNotAllowed = 405;
		public const internalServerError = 500;
		
		public static function ok() {
			header("HTTP/1.1 200 OK");
			header("Content-Type: text/plain");
		}

		public static function unauthorized() {
			header("HTTP/1.1 401 Unauthorized");
			header("Content-Type: text/plain");
		}

		public static function forbidden() {
			header("HTTP/1.1 403 Forbidden");
			header("Content-Type: text/plain");
		}

		public static function notFound() {
			header("HTTP/1.1 404 Not Found");
			header("Content-Type: text/plain");
		}

		public static function methodNotAllowed() {
			header("HTTP/1.1 405 Method Not Allowed");
			header("Content-Type: text/plain");
		}

		public static function internalServerError() {
			header("HTTP/1.1 500 Internal Server Error: ");
			header("Content-Type: text/plain");
		}

		public static function movePermanently() {
			header("HTTP/1.1 301 Moved Permanently");
		}

		public static function found() {
			header("HTTP/1.1 302 Found");
		}
	}
?>