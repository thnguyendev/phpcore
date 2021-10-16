<?php
namespace PHPCore;

class RouteProperties extends Enum
{
    const Methods = "Methods";
    const Path = "Path";
    const Parameters = "Parameters";
    const Controller = "Controller";
    const View = "View";
    const Redirect = "Redirect";
    const Authorized = "Authorized";
    const Roles = "Roles";
}
?>