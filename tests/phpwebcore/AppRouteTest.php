<?php
use PHPUnit\Framework\TestCase;
use PHPWebCore\AppRoute;
use PHPWebCore\RouteProperty;
use PHPWebCore\HttpMethod;
use PHPWebCore\NotFoundException;

class AppRouteTest extends TestCase
{
    public function testAppRoute()
    {
        $routing = new Route();
        $routing->initialize();
        $route = $routing->getRoute(HttpMethod::Get, "/");
        $this->assertNotEmpty($route);
        $this->expectException(NotFoundException::class);
        $route = $routing->getRoute(HttpMethod::Post, "/test");
    }
}

class Route extends AppRoute
{
    public function initialize()
    {
        $this->routes = 
        [
            [
                // Root path can be empty or "/"
                RouteProperty::Path => "",
                // HTTP method attached to this action. If no declaration then all methods are accepted
                RouteProperty::Methods => [HttpMethod::Get, HttpMethod::Post],
            ]
        ];
    }
}
?>