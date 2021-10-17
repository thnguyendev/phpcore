<?php
namespace App;

use PHPWebCore\AppRoute;
use PHPWebCore\RouteProperties;
use App\Controllers\HomeController;

class Route extends AppRoute
{
    public function initialize()
    {
        $this->routes = 
        [
            [
                RouteProperties::Path => "",
                RouteProperties::Controller => HomeController::class,
                RouteProperties::Action => "index",
                RouteProperties::View => "Views/HomeView",
            ],
            [
                RouteProperties::Path => "",
                RouteProperties::Parameters => ["name"],
                RouteProperties::Controller => HomeController::class,
                RouteProperties::Action => "index",
                RouteProperties::View => "Views/HomeView",
            ],
        ];
    }
}
?>