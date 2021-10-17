<?php
namespace App;

use PHPWebCore\App;

class Bootstrap extends App
{
    public function process()
    {
        // Add services to container
        //$this->container = $this->container
        //    ->withSingleton(DatabaseSeviceInterface::class, DatabaseService::class)
        //    ->withTransient(DoSomethingServiceInterface::class, DoSomethingService::class);

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
    }
}
?>
