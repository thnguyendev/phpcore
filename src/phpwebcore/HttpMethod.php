<?php
namespace PHPWebCore;

class HttpMethod extends Enum
{
    const Get = "GET";
    const Post = "POST";
    const Put = "PUT";
    const Delete = "DELETE";
    const Patch = "PATCH";
    const Options = "OPTIONS";
}
?>