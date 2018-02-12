# phpcore 2.0.1
phpcore framework is a simple and easy to use MVC framework for web application development. It has been developed for the ease of building web application in PHP with OOP and MVC framework. It could be useful for projects in education.

## Quick start
1. Download and install Composer by the following url https://getcomposer.org/download/
2. Create phpcore project by Composer. Execute below commands
    ```
    > composer create-project thnguyendev/phpcore [project folder]
    > cd [project folder]
    ```
3. Configure web server
    * Apache server
    Modify .htaccess file of project as per following
    ```
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?q=$1 [QSA,NC,L]
    ```
    * Nginx server
    Insert following codes into your server configuration in nginx.conf
    ```
    location / {
        index  index.html index.htm index.php;
        if (!-e $request_filename) {
            rewrite ^(.*)$ /index.php?q=$1;
        }
    }
    ```
4. Modify startup file [project folder]/src/server/Startup.php
    ```php
    <?php
        namespace phpcore;

        use Exception;
        use phpcore\core\App;
        use phpcore\core\Route;

        class Startup extends App {
            // declare api controllers
            public $ApiRoutes = array();

            // declare web controllers
            public $WebRoutes = array(
                "namespace" => "phpcore\\controllers\\",
                "" => array("controller" => "HomeController"),
                "home" => array("controller" => "HomeController")
            );

            public function __construct() {
                try {
                    parent::__construct();
                    $this->Route->setApiRoutes($this->ApiRoutes);
                    $this->Route->setWebRoutes($this->WebRoutes);
                }
                catch (Exception $e) {
                    throw $e;
                }
            }

            public function process() {
                try {
                    if ($this->Request->IsApi) {
                        $this->useCors("*");
                    }

                    $this->useMvc();
                }
                catch (Exception $e) {
                    throw $e;
                }
            }
        }
    ?>

    ```
5. Create HomeController [project folder]/src/server/controllers/HomeController.php
    ```php
    <?php
        namespace phpcore\controllers;

        use phpcore\core\Controller;

        class HomeController extends Controller {
            public function process() {
                try {
                    $this->view("Home");
                }
                catch (Exception $e) {
                    throw $e;
                }
            }
        }
    ?>
    ```
6. Create a view for HomeController [project folder]/src/server/views/Home.php
    ```html
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>PHP Core Quick Start</title>
    </head>
    <body>
        <h1>PHP Core Quick Start</h1>
    </body>
    </html>
    ```

## Web API
Steps to create a Web API with phpcore framework.
1. Follow steps 1 to 3 from Quick start to setup new project.
2. Modify startup file [project folder]/src/server/Startup.php
    ```php
    <?php
        namespace phpcore;

        use Exception;
        use phpcore\core\App;
        use phpcore\core\Route;

        class Startup extends App {
            // declare api controllers
            public $ApiRoutes = array(
                "namespace" => "phpcore\\controllers\\api\\",
                "getinfo" => array("controller" => "GetInfoController")
            );
            
            // declare web controllers
            public $WebRoutes = array();
            
            public function __construct() {
                try {
                    parent::__construct();
                    $this->Route->setApiRoutes($this->ApiRoutes);
                    $this->Route->setWebRoutes($this->WebRoutes);
                }
                catch (Exception $e) {
                    throw $e;
                }
            }

            public function process() {
                try {
                    if ($this->Request->IsApi) {
                        $this->useCors("*");
                    }

                    $this->useMvc();
                }
                catch (Exception $e) {
                    throw $e;
                }
            }
        }
    ?>
    ```
3. Create an API controller [project folder]/src/server/controllers/api/GetInfoController.php
    ```php
    <?php
        namespace phpcore\controllers\api;

        use phpcore\core\ApiController;
        use phpcore\core\HttpCodes;

        class GetInfoController extends ApiController {
            public function get() {
                try {
                    header("Content-Type: application/json");
                    echo("{'Name':'PHP Core','Author':'Hung Thanh Nguyen'}");
                }
                catch (Exception $e) {
                    throw $e;
                }
            }

            public function options() {
                try {
                    HttpCodes::ok();
                }
                catch (Exception $e) {
                    throw $e;
                }
            }

            public function post() {
                try {
                    HttpCodes::methodNotAllowed();
                }
                catch (Exception $e) {
                    throw $e;
                }
            }

            public function put() {
                try {
                    HttpCodes::methodNotAllowed();
                }
                catch (Exception $e) {
                    throw $e;
                }
            }

            public function delete() {
                try {
                    HttpCodes::methodNotAllowed();
                }
                catch (Exception $e) {
                    throw $e;
                }
            }

            public function patch() {
                try {
                    HttpCodes::methodNotAllowed();
                }
                catch (Exception $e) {
                    throw $e;
                }
            }
        }
    ?>
    ```

## Use Doctrine 2 to work with database
This example will create a Web API that returns data from SQLite with Doctrine, make sure that SQLite PDO has been enabled in PHP configuration.
1. Follow steps 1 to 3 from Quick start to setup new project.
2. Modify file [project folder]/composer.json
    ```json
    {
        "name": "thnguyendev/phpcore",
        "description": "The phpcore framework.",
        "version": "2.0.1",
        "keywords": ["framework", "phpcore"],
        "license": "MIT",
        "type": "project",
        "autoload": {
            "psr-4": {
                "phpcore\\": "src/server"
            }
         },
        "require": {
            "doctrine/orm": "2.5.*"
        }
    }
    ```
    Run below command in console to update project
    ```
    > composer update
    ```
3. Create Info class [project folder]/src/server/models/Info.php
    ```php
    <?php
        namespace phpcore\models;

        use Doctrine\ORM\Mapping as ORM;

        /**
        * @ORM\Entity @ORM\Table(name="Info")
        **/
        class Info {
            /**
            * @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue
            * @var int
            **/
            public $ID;

            /**
            * @ORM\Column(type="string")
            * @var string
            **/
            public $Name;

            /**
            * @ORM\Column(type="string")
            * @var string
            **/
            public $Author;
        }
    ?>
    ```
4. Create DataContext class [project folder]/src/server/models/DataContext.php
    ```php
    <?php
        namespace phpcore\models;

        use Exception;
        use Doctrine\ORM\Tools\Setup;
        use Doctrine\ORM\EntityManager;

        class DataContext {

            protected $EntityManager;

            public function __construct() {
                try {
                    // Create a simple "default" Doctrine ORM configuration for Annotations
                    $isDevMode = true;
                    $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/"), $isDevMode, null, null, false);

                    // database configuration parameters
                    $conn = array(
                        'driver' => 'pdo_sqlite',
                        'path' => __DIR__ . '/../db.sqlite',
                    );

                    // obtaining the entity manager
                    $this->EntityManager = EntityManager::create($conn, $config);
                }
                catch (Exception $e) {
                    throw $e;
                }
            }

            public function initialize() {
                try {
                    $InfoRepository = $this->EntityManager->getRepository("phpcore\\models\\Info");
                    $Info = $InfoRepository->findAll();
                    if (count($Info) == 0) {
                        $NewInfo = new Info();
                        $NewInfo->Name = "PHP Core";
                        $NewInfo->Author = "Hung Thanh Nguyen";
                        $this->EntityManager->persist($NewInfo);
                        $this->EntityManager->flush();
                    }
                }
                catch (Exception $e) {
                    throw $e;
                }
            }

            public function findInfo() {
                try {
                    $InfoRepository = $this->EntityManager->getRepository("phpcore\\models\\Info");
                    $Info = $InfoRepository->findAll();
                    return $Info;
                }
                catch (Exception $e) {
                    throw $e;
                }
            }

            public function getEntityManager() {
                try {
                    return $this->EntityManager;
                }
                catch (Exception $e) {
                    throw $e;
                }
            }
        }
    ?>
    ```
5. Create database schema with Doctrine command-line interface. First, create configuration file [project folder]/cli-config.php
    ```php
    <?php
        require_once "vendor/autoload.php";

        use phpcore\models\DataContext;

        $context = new DataContext();
        return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($context->getEntityManager());
    ?>
    ```
    Then run below command
    ```
    > vendor/bin/doctrine orm:schema-tool:create
    ```
6. Modify startup file [project folder]/src/server/Startup.php
    ```php
    <?php
        namespace phpcore;

        use Exception;
        use phpcore\core\App;
        use phpcore\core\Route;

        class Startup extends App {
            // declare api controllers
            public $ApiRoutes = array(
                "namespace" => "phpcore\\controllers\\api\\",
                "getinfo" => array("controller" => "GetInfoController")
            );
            
            // declare web controllers
            public $WebRoutes = array();
            
            public function __construct() {
                try {
                    parent::__construct();
                    $this->Route->setApiRoutes($this->ApiRoutes);
                    $this->Route->setWebRoutes($this->WebRoutes);
                }
                catch (Exception $e) {
                    throw $e;
                }
            }

            public function process() {
                try {
                    if ($this->Request->IsApi) {
                        $this->useCors("*");
                    }

                    $this->useMvc();
                }
                catch (Exception $e) {
                    throw $e;
                }
            }
        }
    ?>
    ```
7. Create an API controller [project folder]/src/server/controllers/api/GetInfoController.php
    ```php
    <?php
        namespace phpcore\controllers\api;

        use phpcore\core\ApiController;
        use phpcore\core\HttpCodes;
        use phpcore\models\DataContext;

        class GetInfoController extends ApiController {
            public function get() {
                try {
                    $DataContext = new DataContext();
                    $DataContext->initialize();
                    header("Content-Type: application/json");
                    echo(json_encode($DataContext->findInfo()));
                }
                catch (Exception $e) {
                    throw $e;
                }
            }

            public function options() {
                try {
                    HttpCodes::ok();
                }
                catch (Exception $e) {
                    throw $e;
                }
            }

            public function post() {
                try {
                    HttpCodes::methodNotAllowed();
                }
                catch (Exception $e) {
                    throw $e;
                }
            }

            public function put() {
                try {
                    HttpCodes::methodNotAllowed();
                }
                catch (Exception $e) {
                    throw $e;
                }
            }

            public function delete() {
                try {
                    HttpCodes::methodNotAllowed();
                }
                catch (Exception $e) {
                    throw $e;
                }
            }

            public function patch() {
                try {
                    HttpCodes::methodNotAllowed();
                }
                catch (Exception $e) {
                    throw $e;
                }
            }
        }
    ?>
    ```
