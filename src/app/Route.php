<?php
namespace App;

use PHPWebCore\AppRoute;
use PHPWebCore\RouteProperty;
use App\Controllers\HomeController;

class Route extends AppRoute
{
    public function initialize()
    {
        $this->routes = 
        [
            [
                RouteProperty::Path => "",
                RouteProperty::Controller => HomeController::class,
                RouteProperty::Action => "index",
                RouteProperty::View => "Views/HomeView",
            ],
            [
                RouteProperty::Path => "",
                RouteProperty::Parameters => ["name"],
                RouteProperty::Controller => HomeController::class,
                RouteProperty::Action => "index",
                RouteProperty::View => "Views/HomeView",
            ],
        ];
    }
}
?>