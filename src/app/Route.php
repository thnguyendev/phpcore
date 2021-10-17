<?php
namespace App;

use PHPWebCore\AppRoute;
use PHPWebCore\RouteProperty;
use PHPWebCore\HttpMethod;
use App\Controllers\HomeController;

class Route extends AppRoute
{
    public function initialize()
    {
        $this->routes = 
        [
            [
                // Root path can be empty or "/"
                RouteProperty::Path => "",
                // Parameters is an a array of string, contains all parameters' names
                RouteProperty::Controller => HomeController::class,
                // Method name
                RouteProperty::Action => "index",
                // View file name with full path. The root is "app" folder
                RouteProperty::View => "Views/HomeView",
                // HTTP method attached to this action. If no declaration then all methods are accepted
                //RouteProperty::Parameters => ["name"],
                // Full class name with namespace. "App" is root namespace of the app
                //RouteProperty::Methods => [HttpMethod::Get, HttpMethod::Post],
                // The origins accepted in CORS, it could be a string or an array.
                // A string "*" will accept all
                //RouteProperty::AllowedOrigins => ["http://localhost", "http://localhost:8080"],
                // If true, this action need to be authorized
                //RouteProperty::Authorized => true,
                // Roles that allow to have access to this action
                //RouteProperty::Roles => ["Admin", "User"],
                // Redirect to the url
                //RouteProperty::Redirect => "http://localhost/UnderConstruction",
            ],
            [
                // Root path can be empty or "/"
                RouteProperty::Path => "/",
                // Parameters is an a array of string, contains all parameters' names
                RouteProperty::Parameters => ["name"],
                // Full class name with namespace. "App" is root namespace of the app
                RouteProperty::Controller => HomeController::class,
                // Method name
                RouteProperty::Action => "index",
                // View file name with full path. The root is "app" folder
                RouteProperty::View => "Views/HomeView",
            ],
        ];
    }
}
?>