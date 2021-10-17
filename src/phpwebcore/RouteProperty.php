<?php
namespace PHPWebCore;

class RouteProperty extends Enum
{
    const Methods = "Methods";
    const Path = "Path";
    const Parameters = "Parameters";
    const Controller = "Controller";
    const Action = "Action";
    const View = "View";
    const Redirect = "Redirect";
    const Authorized = "Authorized";
    const Roles = "Roles";
    const AllowedOrigins = "AllowedOrigins";
}
?>