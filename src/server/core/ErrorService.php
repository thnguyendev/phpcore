<?php
    namespace phpcore\core;

    use Exception;

    class ErrorService implements ErrorServiceInterface {
        public function process(Exception $exception) {
            error_log($exception->getMessage());

            switch($exception->getCode()) {
                case HttpCodes::unauthorized:
                    HttpCodes::unauthorized();
                break;
                case HttpCodes::forbidden:
                    HttpCodes::forbidden();
                break;
                case HttpCodes::notFound:
                    HttpCodes::notFound();
                break;
                case HttpCodes::methodNotAllowed:
                    HttpCodes::methodNotAllowed();
                break;
                default:
                    HttpCodes::internalServerError();
            }
            exit();
        }
    }
?>