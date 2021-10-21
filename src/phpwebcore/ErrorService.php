<?php
namespace PHPWebCore;

use Psr\Http\Message\Response;

class ErrorService implements ErrorServiceInterface
{
    public function process(\Throwable $e)
    {
        $time = date("Y-m-d H:i:s (T)");
        error_log("[{$time}] Error: {$e->getFile()} | {$e->getLine()} | {$e->getMessage()}".PHP_EOL."{$e->getTraceAsString()}");
        $code = $e->getCode();
        if (!is_int($code) || $code < 100 || $code > 999)
            $code = 500;
        $reasonPhrase = "";
        if (key_exists($code, Response::ReasonPhrase))
            $reasonPhrase = Response::ReasonPhrase[$code];
        header(Initialization::getProtocol()." {$code} {$reasonPhrase}", true);
        echo $e->getMessage();
    }
}
?>