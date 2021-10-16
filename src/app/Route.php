<?php
namespace App;

use PHPCore\AppRoute;

class Route extends AppRoute
{
    public function initialize()
    {
        $this->mapping = 
        [
            [
                AppRoute::RoutePath => "",
                AppRoute::RouteController => HomeController::class,
                AppRoute::RouteView => "HomeView",
            ],
            [
                AppRoute::RoutePath => "",
                AppRoute::RouteParameters => ["name"],
                AppRoute::RouteController => HomeController::class,
                AppRoute::RouteView => "HomeView",
            ],
        ];
    }
}
?>