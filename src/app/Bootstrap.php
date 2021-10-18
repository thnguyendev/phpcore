<?php
namespace App;

use App\Services\DatabaseService;
use App\Services\ProjectServiceInterface;
use App\Services\ProjectService;
use PHPWebCore\App;

class Bootstrap extends App
{
    public function process()
    {
        // Initialize Database
        $db = new DatabaseService("sqlite:".static::getAppFolder()."/Project.db");

        // Add services to container
        $this->container = $this->container
            ->withTransient(ProjectServiceInterface::class, ProjectService::class);

        // Redirect to HTTPS
        //$this->useHttps();

        // Allow CORS
        //$this->allowCors();

        // Add default routing
        $this->setRouting(new Route());
        
        // Use routing to map route
        $this->useRouting();

        // Middleware before passing request to controller such as Authorization can apply here

        // Invoke the action to fulfill the request
        // Data likes user information from Authorization can be passed to controller by bucket
        $this->invokeAction(bucket: null);

        // Middleware after invoke the action can be here

        // Close Database connection
        $db->close();
    }
}
?>
