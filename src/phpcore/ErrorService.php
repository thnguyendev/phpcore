<?php
namespace PHPCore;

use Psr\Http\Message\ResponseFactory;

class ErrorService implements ErrorServiceInterface {
    private $responseFactory;

    public function __construct(ResponseFactory $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function process(\Throwable $e)
    {
        error_log($e->getMessage());
        $code = $e->getCode();
        if (!is_int($code) || $code < 100 || $code > 999)
            $code = 500;
        $reasonPhrase = $this->responseFactory->createResponse($code)->getReasonPhrase();
        header("{$_SERVER["SERVER_PROTOCOL"]} {$reasonPhrase}");
        header("Content-Type: text/plain");
        echo $e->getMessage();
    }
}
?>