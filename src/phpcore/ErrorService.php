<?php
namespace PHPCore;

use Psr\Http\Message\Response;

class ErrorService implements ErrorServiceInterface {
    public function process(\Throwable $e)
    {
        $time = date("Y-m-d H:i:s (T)");
        error_log("[{$time}] Error: {$e->getFile()} | {$e->getLine()} | {$e->getMessage()}\r\n{$e->getTraceAsString()}");
        $code = $e->getCode();
        if (!is_int($code) || $code < 100 || $code > 999)
            $code = 500;
        $reasonPhrase = "";
        if (key_exists($code, Response::$defaultReasonPhrase))
            $reasonPhrase = Response::$defaultReasonPhrase[$code];
        header(Initialization::getProtocol() . " {$code} {$reasonPhrase}", true);
        header("Content-Type: text/plain", true);
        echo "[{$time}] Error: {$e->getFile()} | {$e->getLine()} | {$e->getMessage()}\r\n{$e->getTraceAsString()}";
    }
}
?>