<?php
namespace App;

use Psr\Http\Message\ServerRequestInterface;
use PHPWebCore\App;
use PHPWebCore\RouteProperty;
use App\Services\DatabaseService;
use App\Services\ProjectServiceInterface;
use App\Services\ProjectService;
use App\Services\UserServiceInterface;
use App\Services\UserService;
use PHPWebCore\ErrorServiceInterface;

class Bootstrap extends App
{
    public function process()
    {
        $this->container = $this->container
            ->withTransient(ErrorServiceInterface::class, ExceptionHandler::class);
        throw new \Exception();

        // Initialize Database
        $db = new DatabaseService("sqlite:".static::getAppFolder()."/Project.db");

        // Add services to container
        $this->container = $this->container
            ->withTransient(ProjectServiceInterface::class, ProjectService::class)
            ->withSingleton(ServerRequestInterface::class, $this->request)
            ->withTransient(UserServiceInterface::class, UserService::class);

        // Redirect to HTTPS
        //$this->useHttps();

        // Allow CORS
        //$this->allowCors();

        // Add default routing
        $this->setRouting(new Route());
        
        // Use routing to map route
        $this->useRouting();

        $bucket = [];
        // Authorize here
        if (isset($this->route[RouteProperty::Authorized]) && $this->route[RouteProperty::Authorized])
        {
            $userService = $this->container->get(UserServiceInterface::class);
            $bucket["user"] = $userService->authorize();
        }

        // Middleware before passing request to controller such as Authorization can apply here

        // Invoke the action to fulfill the request
        // Data likes user information from Authorization can be passed to controller by bucket
        $this->invokeAction(bucket: $bucket);

        // Middleware after invoke the action can be here

        // Close Database connection
        $db->close();
    }
}
?>
