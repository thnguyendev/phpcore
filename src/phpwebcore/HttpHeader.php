<?php
namespace PHPWebCore;

class HttpHeader extends Enum
{
    const ContentType = "Content-Type";
    const ContentLength = "Content-Length";
    const AccessControlRequestMethod = "Access-Control-Request-Method";
    const AccessControlAllowOrigin = "Access-Control-Allow-Origin";
    const AccessControlAllowMethods = "Access-Control-Allow-Methods";
}
?>