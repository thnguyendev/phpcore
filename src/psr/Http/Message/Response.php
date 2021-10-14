<?php
    namespace Psr\Http\Message;

    class Response extends Message implements ResponseInterface {
        private $statusCode = 200;
        private $reasonPhrase = '';

        public static $defaultReasonPhrase = [
            // Informational 1xx
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            103 => 'Early Hints',
    
            // Successful 2xx
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',
            208 => 'Already Reported',
            226 => 'IM Used',
    
            // Redirection 3xx
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect',
    
            // Client Error 4xx
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Payload Too Large',
            414 => 'URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot',
            421 => 'Misdirected Request',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            425 => 'Too Early',
            426 => 'Upgrade Required',
            428 => 'Precondition Required',
            429 => 'Too Many Requests',
            431 => 'Request Header Fields Too Large',
            451 => 'Unavailable For Legal Reasons',
    
            // Server Error 5xx
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            508 => 'Loop Detected',
            510 => 'Not Extended',
            511 => 'Network Authentication Required',
        ];

        public function getStatusCode() {
            return $this->statusCode;
        }

        public function withStatus($code, $reasonPhrase = '') {
            if (!is_int($code) || $code < 100 || $code > 999)
                throw new \InvalidArgumentException(ErrorMessage::invalidStatusCode);
            $clone = clone $this;
            $clone->statusCode = $code;
            if (is_string($reasonPhrase) && $reasonPhrase !== '') {
                $clone->reasonPhrase = $reasonPhrase;
            }
            else {
                if (key_exists($this->statusCode, static::$defaultReasonPhrase))
                    $clone->reasonPhrase = static::$defaultReasonPhrase[$this->statusCode];
                else
                    $clone->reasonPhrase = '';
            }
            return $clone;
        }

        public function getReasonPhrase() {
            return $this->reasonPhrase;
        }
    }
?>