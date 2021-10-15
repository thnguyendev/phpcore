# PHPCore 4.0.0
PHPCore is a PHP framework. It aims to be simple and easy to use. It implements PSR-7 HTTP message interfaces and PSR-17 HTTP Factories. It also supports dependency injection.

## Quick start
1. Download and install Composer by the following url https://getcomposer.org/download/
2. Create phpcore project by Composer. Execute below commands
    ```
    > composer create-project thnguyendev/phpcore [project folder]
    > cd [project folder]
    > composer update
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
4. Create Routes [project folder]/src/server/models/Routes.php
    ```php
    <?php
        namespace phpcore\models;

        use phpcore\core\RouteDefine;

        class Routes {
            public const paths = array(
                "" => array (
                    RouteDefine::controller => "phpcore\\controllers\\HomeController",
                    RouteDefine::view => "src/server/views/Home.php"
                )
            );
        }
    ?>
    ```
5. Modify startup file [project folder]/src/server/Startup.php
    ```php
    <?php
        namespace phpcore;

        use phpcore\core\App;
        use phpcore\models\Routes;

        class Startup extends App {
            public function __construct() {
                parent::__construct();
                $routeService = $this->getService("phpcore\\core\\RouteService");
                $routeService->setRoutes(Routes::paths);
                $routeService->mapRoute();
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
			public $message;
			public function process() {
				$this->message = "Welcome to PHP Core";
				$this->view();
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
        <title>PHP Core</title>
    </head>
    <body>
        <h1><?php echo $this->message; ?></h1>
    </body>
    </html>
    ```

## Web API
Steps to create a Web API with phpcore framework.
1. Follow steps 1 to 3 from Quick start to setup new project.
2. Create Routes [project folder]/src/server/models/Routes.php
    ```php
    <?php
        namespace phpcore\models;

        use phpcore\core\RouteDefine;

        class Routes {
            public const paths = array(
                "getinfo" => array (
                    RouteDefine::controller => "phpcore\\controllers\\GetInfoController"
                )
            );
        }
    ?>
    ```
2. Modify startup file [project folder]/src/server/Startup.php
    ```php
    <?php
        namespace phpcore;

        use phpcore\core\App;
        use phpcore\models\Routes;

        class Startup extends App {
            public function __construct() {
                parent::__construct();
                $this->enableCors();
                $routeService = $this->getService("phpcore\\core\\RouteService");
                $routeService->setRoutes(Routes::paths);
                $routeService->mapRoute();
            }
        }
    ?>
    ```
3. Create an API controller [project folder]/src/server/controllers/api/GetInfoController.php
    ```php
    <?php
        namespace phpcore\controllers;

        use phpcore\core\ApiController;
        use phpcore\core\ContentType;

        class GetInfoController extends ApiController {
            public function get() {
                ContentType::applicationJson();
                echo("{ 'Name': 'PHP Core', 'Author': 'Hung Thanh Nguyen' }");
            }
        }
    ?>
    ```

## Use Doctrine ORM to work with Sqlite database
This example will create a Web API that returns data from SQLite with Doctrine, make sure that SQLite PDO has been enabled in PHP configuration.
1. Follow steps 1 to 3 from Quick start to setup new project.
2. Modify file [project folder]/composer.json
    ```json
    {
        "name": "thnguyendev/phpcore",
        "description": "The phpcore framework.",
        "version": "3.0.0",
        "keywords": ["framework", "phpcore"],
        "license": "MIT",
        "type": "project",
        "autoload": {
            "psr-4": {
                "phpcore\\": "src/server"
            }
         },
        "require": {
            "doctrine/orm": "*"
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
        * @ORM\Entity
        * @ORM\Table(name="Info")
        **/
        class Info {
            /**
            * @ORM\Id
            * @ORM\Column(type="integer")
            * @ORM\GeneratedValue
            * @var int
            **/
            private $ID;

            /**
            * @ORM\Column(type="string")
            * @var string
            **/
            private $Name;

            /**
            * @ORM\Column(type="string")
            * @var string
            **/
            private $Author;

            public function getID() {
                return $this->ID;
            }

            public function getName() {
                return $this->Name;
            }

            public function setName($name) {
                $this->Name = $name;
            }

            public function getAuthor() {
                return $this->Author;
            }

            public function setAuthor($author) {
                $this->Author = $author;
            }
        }
    ?>
    ```
4. Create DataService class [project folder]/src/server/services/DataService.php
    ```php
    <?php
        namespace phpcore\services;

        use Exception;
        use Doctrine\ORM\Tools\Setup;
        use Doctrine\ORM\EntityManager;
        use phpcore\models\Info;

        class DataService {

            private $entityManager;

            public function __construct() {
                try {
                    // Create a simple "default" Doctrine ORM configuration for Annotations
                    $isDevMode = true;
                    $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/../models"), $isDevMode, null, null, false);

                    // database configuration parameters for Sqlite
                    $conn = array(
                        'driver' => 'pdo_sqlite',
                        'path' => __DIR__ . '/../db.sqlite',
                    );

                    // obtaining the entity manager
                    $this->entityManager = EntityManager::create($conn, $config);
                }
                catch (Exception $e) {
                    throw $e;
                }
            }

            public function initialize() {
                try {
                    $infoRepository = $this->entityManager->getRepository("phpcore\\models\\Info");
                    $info = $infoRepository->findAll();
                    if (count($info) == 0) {
                        $newInfo = new Info();
                        $newInfo->setName("PHP Core");
                        $newInfo->setAuthor("Hung Thanh Nguyen");
                        $this->entityManager->persist($newInfo);
                        $this->entityManager->flush();
                    }
                }
                catch (Exception $e) {
                    throw $e;
                }
            }

            public function getEntityManager() {
                return $this->entityManager;
            }
        }
    ?>
    ```
5. Create database schema with Doctrine command-line interface. First, create configuration file [project folder]/cli-config.php
    ```php
    <?php
        require_once "vendor/autoload.php";

        use phpcore\services\DataService;

        $dataService = new DataService();
        return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($dataService->getEntityManager());
    ?>
    ```
    Then run below command
    ```
    > "vendor/bin/doctrine" orm:schema-tool:create
    ```
6. Create Routes [project folder]/src/server/models/Routes.php
    ```php
    <?php
        namespace phpcore\models;

        use phpcore\core\RouteDefine;

        class Routes {
            public const paths = array(
                "getinfo" => array (
                    RouteDefine::controller => "phpcore\\controllers\\GetInfoController"
                )
            );
        }
    ?>
    ```
7. Modify startup file [project folder]/src/server/Startup.php
    ```php
    <?php
        namespace phpcore;

        use phpcore\core\App;
        use phpcore\models\Routes;
        use phpcore\services\DataService;

        class Startup extends App {
            public function __construct() {
                parent::__construct();
                $this->enableCors();
                $routeService = $this->getService("phpcore\\core\\RouteService");
                $routeService->setRoutes(Routes::paths);
                $routeService->mapRoute();

                $this->addService(new DataService());
                $this->getService("phpcore\\services\\DataService")->initialize();
            }
        }
    ?>
    ```
8. Create an API controller [project folder]/src/server/controllers/api/GetInfoController.php
    ```php
    <?php
        namespace phpcore\controllers;

        use Exception;
        use phpcore\core\ApiController;
        use phpcore\core\ContentType;
        use phpcore\core\HttpCodes;

        class GetInfoController extends ApiController {
            public function get() {
                $dataService = $this->getApp()->getService("phpcore\\services\\DataService");
                if (!isset($dataService)) {
                    throw new Exception("Data service not found", HttpCodes::internalServerError);
                }
                $entityManager = $dataService->getEntityManager();
                $infoRepository = $entityManager->getRepository("phpcore\\models\\Info");
                $info = $infoRepository->findAll();
                if (count($info) > 0) {
                    ContentType::applicationJson();
                    printf("{ 'Name': '%s', 'Author': '%s' }", $info[0]->getName(), $info[0]->getAuthor());
                }
            }
        }
    ?>
    ```

## Firebase jwt authorization
This example demonstrate authentication with Firebase Jwt.
1. Follow steps 1 to 3 from Quick start to setup new project.
2. Modify file [project folder]/composer.json
    ```json
    {
        "name": "thnguyendev/phpcore",
        "description": "The phpcore framework.",
        "version": "3.0.0",
        "keywords": ["framework", "phpcore"],
        "license": "MIT",
        "type": "project",
        "autoload": {
            "psr-4": {
                "phpcore\\": "src/server"
            }
         },
        "require": {
            "firebase/php-jwt": "*"
        }
    }
    ```
    Run below command in console to update project
    ```
    > composer update
    ```
3. Create AuthorizationService class [project folder]/src/server/services/AuthorizationService.php
    ```php
    <?php
        namespace phpcore\services;

        use Exception;
        use phpcore\core\HttpCodes;
        use phpcore\core\RouteDefine;
        use Firebase\JWT\JWT;

        class AuthorizationService {
            private const key = "example_key";
            private $app;

            public function __construct($app) {
                $this->app = $app;
            }

            public function authenticate($input) {
                $jwt = null;   
                if ($input->userName === "admin" && $input->password === "nopassword") {
                    $requestService = $this->app->getService("phpcore\\core\\RequestService");
                    if (!isset($requestService)) {
                        throw new Exception("Request service not found", HttpCodes::internalServerError);
                    }
                    $time = time();
                    $payload = [
                        'iss' => $requestService->getServer()["Name"],
                        'iat' => $time,
                        'nbf' => $time + 10,
                        'exp' => $time + 600,
                        'user' => [
                            'userName' => $input->userName
                        ]
                    ];
                    $jwt = JWT::encode($payload, $this::key);
                }
                return $jwt;
            }

            public function authorize() {
                $requestService = $this->app->getService("phpcore\\core\\RequestService");
                if (!isset($requestService)) {
                    throw new Exception("Request service not found", HttpCodes::internalServerError);
                }
                $routeService = $this->app->getService("phpcore\\core\\RouteService");
                if (!isset($routeService)) {
                    throw new Exception("Route service not found", HttpCodes::internalServerError);
                }
                $authorized = true;
                $route = $routeService->getRoute();
                if (isset($route)) {
                    if (isset($route[RouteDefine::authorize]) && $route[RouteDefine::authorize] === true) {
                        $authorized = false;
                        if (isset($requestService->getHeader()["Authorization"])) {
                            list($jwt) = sscanf($requestService->getHeader()["Authorization"], "Bearer %s");
                            if ($jwt) {
                                try {
                                    $token = JWT::decode($jwt, $this::key, array("HS256"));
                                    $authorized = true;
                                }
                                catch(Exception $e) { }
                            }
                        }
                    }
                }
                if (!$authorized) {
                    throw new Exception("Authorization failed", HttpCodes::unauthorized);
                }
            }
        }
    ?>
    ```
4. Create Routes [project folder]/src/server/models/Routes.php
    ```php
    <?php
        namespace phpcore\models;

        use phpcore\core\RouteDefine;

        class Routes {
            public const paths = array(
                "authenticate" => array (
                    RouteDefine::controller => "phpcore\\controllers\\AuthenticateUserController"
                ),
                "getinfo" => array (
                    RouteDefine::controller => "phpcore\\controllers\\GetInfoController",
                    RouteDefine::authorize => true
                )
            );
        }
    ?>
    ```
5. Modify startup file [project folder]/src/server/Startup.php
    ```php
    <?php
        namespace phpcore;

        use phpcore\core\App;
        use phpcore\models\Routes;
        use phpcore\services\AuthorizationService;

        class Startup extends App {
            public function __construct() {
                parent::__construct();
                $this->enableCors();
                $routeService = $this->getService("phpcore\\core\\RouteService");
                $routeService->setRoutes(Routes::paths);
                $routeService->mapRoute();

                $this->addService(new AuthorizationService($this));
                $this->getService("phpcore\\services\\AuthorizationService")->authorize();
            }
        }
    ?>
    ```
6. Create Authenticate User API controller [project folder]/src/server/controllers/api/AuthenticateUserController.php
    ```php
    <?php
        namespace phpcore\controllers;

        use phpcore\core\ApiController;
        use phpcore\core\ContentType;

        class AuthenticateUserController extends ApiController {
            public function post() {
                $requestService = $this->getApp()->getService("phpcore\\core\\RequestService");
                if (!isset($requestService)) {
                    throw new Exception("Request service not found", HttpCodes::internalServerError);
                }
                $authorizationService = $this->getApp()->getService("phpcore\\services\\AuthorizationService");
                if (!isset($authorizationService)) {
                    throw new Exception("Authorization service not found", HttpCodes::internalServerError);
                }
                $input = json_decode($requestService->getBody());
                $jwt = $authorizationService->authenticate($input);
                ContentType::applicationJson();
                printf("{ 'token': '%s' }", $jwt);
            }
        }
    ?>
    ```
7. Create an API controller [project folder]/src/server/controllers/api/GetInfoController.php
    ```php
    <?php
        namespace phpcore\controllers;

        use Exception;
        use phpcore\core\ApiController;
        use phpcore\core\ContentType;
        use phpcore\core\HttpCodes;

        class GetInfoController extends ApiController {
            public function get() {
                ContentType::applicationJson();
                echo("{ 'Name': 'PHP Core', 'Author': 'Hung Thanh Nguyen' }");
            }
        }
    ?>
    ```
## Running tests
Run the unit tests
    ```
    > ./vendor/bin/phpunit tests
    ```
