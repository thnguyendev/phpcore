<?php
namespace App;

use PHPCore\AppRoute;
use PHPCore\RouteProperties;

class Route extends AppRoute
{
    public function initialize()
    {
        $this->mapping = 
        [
            [
                RouteProperties::Path => "",
                RouteProperties::Controller => HomeController::class,
                RouteProperties::View => "HomeView",
            ],
            [
                RouteProperties::Path => "",
                RouteProperties::Parameters => ["name"],
                RouteProperties::Controller => HomeController::class,
                RouteProperties::View => "HomeView",
            ],
        ];
    }
}
?>