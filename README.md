# PHPWebCore 4.0.0
PHPWebCore is a MVC framework in PHP. It is built on the habits of using ASP.NET Core. It aims to be simple and easy to use. PHPWebCore implements PSR-7 HTTP message interfaces and PSR-17 HTTP Factories. It also supports dependency injection.

## Quick start
1. PHPWebCore needs Composer and of course PHP. Make sure you download and install [PHP](https://www.php.net/downloads.php) and [Composer](https://getcomposer.org/download).
2. Create PHPWebCore project by Composer. Then, run the update command from Composer to download all of denpendencies.
    ```shell
    composer create-project thnguyendev/phpwebcore [project name]
    cd [project name]
    composer update
    ```
3. The web root folder is "public" in project folder. There are several ways to run the app: use the PHP built-in server, Apache server or Nginx server, etc.. For PHP built-in server, you just need to set the document root is "public" folder. In Apache server, .htaccess file is ready in "public" folder, you need to set "public" folder is Apache web root directory. If you use Nginx server, you need to add a server in nginx.conf which has root points to "public" folder in app and setup location like below.
    * PHP built-in server
    ```shell
    php -S localhost -t <path to project folder>/public
    ```
    * Apache server .htaccess config
    ```apacheconf
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?q=$1 [QSA,NC,L]
    ```
    * Nginx server nginx.conf config
    ```nginx
    root        <path to project folder>/public;
    location / {
        index  index.html index.htm index.php;
        if (!-e $request_filename) {
            rewrite ^(.*)$ /index.php?q=$1;
        }
    }
    ```
4. Now back to the app, your workspace in the app is just inside the "src/app" folder. Working with the routes of web app is our first step. PHPWebCore does not use the PHP attributes for the routing. The default routing is the Route class extends from PHPWebCore\AppRoute in Route.php. You need to implement initialize() method for Route class. In this example, we create 2 routes: one is the root path and the other is also the root path but it has "name" as parameter.
    ```php
    namespace App;

    use PHPWebCore\AppRoute;
    use PHPWebCore\RouteProperty;
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
    ```
5. As you see, the routes need HomeController class with the method index(). A controller class can have any name that you like but it must be derived from PHPWebCore/Controller class. The name "HomeController" comes from ASP.NET Core. Moreover, the index() method can 1 parameter or nothing at all. The index() method will call view() method and pass "name" to $args. Now, we create a folder name "Controllers" inside "app" folder and create a file "HomeController.php". Please note that the name of the php file must be the same as the class name.
    ```php
    namespace App\Controllers;

    use PHPWebCore\Controller;

    class HomeController extends Controller
    {
        public function index(string $name = null)
        {
            $this->view(["name" => $name]);
        }
    }
    ```
6. The routes also need a view for the controller. It recommends to use HTML or PHP for the view file. You could put PHP codes inside your HTML template. According to our declaration in routers, the app will look for the view "HomeView", "HomeView.php" or "HomeView.html" in "Views" folder inside "app" folder. So, we create "Views" folder inside "src/app", then create "HomeView.php" inside "Views" folder. 
    ```html
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>PHPWebCore</title>
    </head>
    <body>
        <h1>Hi <?php echo isset($args["name"]) ? $args["name"] : "there" ?>, welcome to PHPWebCore!</h1>
    </body>
    </html>
    ```
7. So, everything is ready except the last step, the app entry point. Default PHPWebCore app entry class is Bootstrap, which is devired from PHPWebCore/App. Yes, it is Bootstrap instead of Startup. Did you feel the ASP.NET Core until now :D? We need to implement process() method. We will make flow of processing of the app here, such as add servcies to app's container, redirect to HTTPS, allow CORS (origin only), use routing, invoke action, etc... You can also run middlewares here, before and after invoke action like authorization. In this example, we only use routing and invoke action after that.
    ```php
    namespace App;

    use PHPWebCore\App;

    class Bootstrap extends App
    {
        public function process()
        {
            // Add default routing
            $this->setRouting(new Route());
            
            // Use routing to map route
            $this->useRouting();

            // Invoke the action to fulfill the request
            // Data likes user information from Authorization can be passed to controller by bucket
            $this->invokeAction(bucket: null);
        }
    }
    ```
Finally, your first PHPWebCore app is ready. Run your app and try it. Use the following Urls in your browser:
    + http://[your host]
    - http://[your host]/[name]
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
    composer update
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
    ./vendor/bin/phpunit tests
    ```
