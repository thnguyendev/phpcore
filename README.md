# PHPWebCore 1.0.0
PHPWebCore is a MVC framework in PHP. It is built on the habits of using ASP.NET Core. It aims to be simple and easy to use. PHPWebCore implements PSR-7 HTTP message interfaces and PSR-17 HTTP Factories. It also supports dependency injection.

## Quick start
1. PHPWebCore needs Composer and of course PHP. Make sure you download and install [PHP](https://www.php.net/downloads.php) and [Composer](https://getcomposer.org/download).
2. Create PHPWebCore project by Composer. Then, run the update command from Composer to download all of denpendencies.
    ```shell
    composer create-project thnguyendev/phpwebcore [project name]
    cd [project name]
    composer install
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
4. Now back to the app, your workspace in the app is just inside the "src/app" folder. Working with the routes of web app is our first step. PHPWebCore does not use the PHP attributes for the routing. The default routing is the Route class extends from PHPWebCore\AppRoute in Route.php. You need to implement initialize() method for Route class. Routes should be defined here. The request Url paths split into paths and parameters. PHPWebCore will map it to the first route that has the most segments in path. In this example, we create 2 routes: one is the root path and the other is also the root path but it has "name" as parameter.
    * Route.php
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
                    // Action method name
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
                    // Action method name
                    RouteProperty::Action => "index",
                    // View file name with full path. The root is "app" folder
                    RouteProperty::View => "Views/HomeView",
                ],
            ];
        }
    }
    ```
5. As you see, the routes need HomeController class with the method index(). A controller class can have any name that you like but it must be derived from PHPWebCore/Controller class. The name "HomeController" comes from ASP.NET Core. Moreover, the index() method can take 1 argument or nothing at all. The index() method will call view() method and pass "name" to $args. Now, we create a folder name "Controllers" inside "app" folder and create a file "HomeController.php". Please note that the name of the php file must be the same as the class name.
    * HomeController.php
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
    * HomeView.php
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
    * Bootstrap.php
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
8. Finally, your first PHPWebCore app is ready. Run your app and try it. Use the following Urls in your browser.
    * http://[your host]
    * http://[your host]/[name]
## Web API
In this tutorial, we will create a PHPWebCore Web API app. First thing first, you need to create a PHPWebCore project.
1. When you have your project, define your API route that uses GET method. This api just simply returns the information of your project in JSON.
    * Route.php
    ```php
    namespace App;

    use PHPWebCore\AppRoute;
    use PHPWebCore\RouteProperty;
    use PHPWebCore\HttpMethod;
    use App\Controllers\ProjectController;

    class Route extends AppRoute
    {
        public function initialize()
        {
            $this->routes = 
            [
                [
                    // Root path can be empty or "/"
                    RouteProperty::Path => "project",
                    // HTTP method attached to this action. If no declaration then all methods are accepted
                    RouteProperty::Methods => [HttpMethod::Get],
                    // Parameters is an a array of string, contains all parameters' names
                    RouteProperty::Controller => ProjectController::class,
                    // Action method name
                    RouteProperty::Action => "getProjectInfo",
                ]
            ];
        }
    }
    ```
2. Next step is creating ProjectController.php of the controller in "Controllers" folder. Set the response content type is application/json.
    * ProjectController.php
    ```php
    namespace App\Controllers;

    use PHPWebCore\Controller;

    class ProjectController extends Controller
    {
        public function getProjectInfo()
        {
            // Return json
            echo "{ 'Project': 'PHPWebCore Api Example', 'Framework': 'PHPWebCore' }";
            // Set content type is application/json
            header("Content-Type: application/json");
        }
    }
    ```
3. It is almost done now. Use the routing and invoke action in your Bootstrap entry class then your app is ready to run.
    * Bootstrap.php
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
4. Is it too simple? Run your project and use below Url in a browser to see your work.
    * http://[your host]/project
## RedBeanPHP and SQLite
This example desmonstrates how your PHPWebCore app work with databases. We use RedBeanPHP and SQLite because it is so easy to included to your app.
1. Before we continue, let's make sure you have SQLite enabled in PHP, created your PHPWebCore and update composer.json to include RedBeanPHP to your app then run update command from Composer
    * php.ini
    ```shell
    extension=pdo_sqlite
    extension=sqlite3
    ```
    * composer.json
    ```json
    {
        "name": "thnguyendev/phpwebcore",
        "description": "PHPWebCore framework",
        "version": "1.0.0",
        "keywords": ["PHPWebCore", "PHP", "MVC framework", "OOP", "PSR-7", "PSR-17", "Dependency Injection"],
        "license": "MIT",
        "type": "project",
        "autoload": {
            "psr-4": {
                "App\\": "src/app",
                "PHPWebCore\\": "src/phpwebcore",
                "Psr\\": "src/psr"
            }
        },
        "require": {
            "php": ">=7.0",
            "psr/http-message": ">=1.0.1",
            "psr/http-factory": ">=1.0.1",
            "gabordemooij/redbean": ">=5.7"
        },
        "require-dev": {
            "phpunit/phpunit": ">=9.5.10"
        }
    }
    ```
    * Run Composer update command
    ```shell
    composer update
    ```
2. We build 2 services for our app, one is DatabaseService to work with SQLite. It creates a connection and initializes the database in the constructor. It also provides a method to stop the connection before the app stops. The other service is ProjectService which provides the data from database. We use this service as a dependency injection of the controller, so we need to build an interface for this service.
You need to create "Services" folder in your app folder to put all of these servies in.
    * DatabaseService.php
    ```php
    namespace App\Services;

    use RedBeanPHP\R;

    class DatabaseService
    {
        public const Project = "porject";
        public const ProjectName = "name";
        public const ProjectFramework = "framework";

        public function __construct(string $connectionString)
        {
            // Open a connection
            R::setup($connectionString);
            $project = R::load(static::Project, 1);
            // No database then create one
            if ($project["id"] === 0)
            {
                $project = R::dispense(static::Project);
                $project[static::ProjectName] = "PHPWebCore with RedBeanPHP and SQLite";
                $project[static::ProjectFramework] = "PHPWebCore";
                R::store($project);
            }
        }

        public function close()
        {
            // Close the connection
            R::close();
        }
    }
    ```
    * ProjectServiceInterface.php
    ```php
    namespace App\Services;

    interface ProjectServiceInterface
    {
        public function getProjectInfo();
    }
    ```
    * ProjectService.php
    ```php
    namespace App\Services;

    use PHPWebCore\NotFoundException;
    use RedBeanPHP\R;

    class ProjectService implements ProjectServiceInterface
    {
        public function getProjectInfo()
        {
            $project = R::load(DatabaseService::Project, 1);
            if ($project["id"] === 0)
                throw new NotFoundException("Project not found", 404);
            return json_encode($project);
        }
    }
    ```
3. The last step we create a controller, declare a route and update the Bootstrap. Beside use routing and invoke controller action in Boostrap, we need to add ProjectService to app's container, initialize the database and close it before app stops.
    * ProjectController.php
    ```php
    namespace App\Controllers;

    use PHPWebCore\Controller;
    use App\Services\ProjectServiceInterface;

    class ProjectController extends Controller
    {
        private $projectService;
        public function __construct(ProjectServiceInterface $projectService)
        {
            $this->projectService = $projectService;
        }

        public function getProjectInfo()
        {
            // Return json
            echo $this->projectService->getProjectInfo();
            // Set content type is application/json
            header("Content-Type: application/json");
        }
    }
    ```
    * Route.php
    ```php
    namespace App;

    use PHPWebCore\AppRoute;
    use PHPWebCore\RouteProperty;
    use PHPWebCore\HttpMethod;
    use App\Controllers\ProjectController;

    class Route extends AppRoute
    {
        public function initialize()
        {
            $this->routes = 
            [
                [
                    // Root path can be empty or "/"
                    RouteProperty::Path => "project",
                    // HTTP method attached to this action. If no declaration then all methods are accepted
                    RouteProperty::Methods => [HttpMethod::Get],
                    // Parameters is an a array of string, contains all parameters' names
                    RouteProperty::Controller => ProjectController::class,
                    // Action method name
                    RouteProperty::Action => "getProjectInfo",
                ]
            ];
        }
    }
    ```
    * Bootstrap.php
    ```php
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

            // Add default routing
            $this->setRouting(new Route());
            
            // Use routing to map route
            $this->useRouting();

            // Invoke the action to fulfill the request
            // Data likes user information from Authorization can be passed to controller by bucket
            $this->invokeAction(bucket: null);

            // Close Database connection
            $db->close();
        }
    }
    ```
4. Now your PHPWebCore app is ready to run. When the first request send to your app. It will creates a SQLite file name "Project.db" in your app folder. Try the following Url
    * http://[your host]/project
## Firebase PHP-JWT authorization
This time we make PHPWebCore app work with PHP-JWT authorization.
1. We create new PHPWebCore project and add Firebase PHP-JWT in
    * composer.json
    ```json
    {
        "name": "thnguyendev/phpwebcore",
        "description": "PHPWebCore framework",
        "version": "1.0.0",
        "keywords": ["PHPWebCore", "PHP", "MVC framework", "OOP", "PSR-7", "PSR-17", "Dependency Injection"],
        "license": "MIT",
        "type": "project",
        "autoload": {
            "psr-4": {
                "App\\": "src/app",
                "PHPWebCore\\": "src/phpwebcore",
                "Psr\\": "src/psr"
            }
        },
        "require": {
            "php": ">=7.0",
            "psr/http-message": ">=1.0.1",
            "psr/http-factory": ">=1.0.1",
            "firebase/php-jwt": ">=5.4.0"
        },
        "require-dev": {
            "phpunit/phpunit": ">=9.5.10"
        }
    }
    ```
    * Run Composer update command
    ```shell
    composer update
    ```
2. We build UserService to provide 2 functions are login() and authorize(). The login() method needs 2 parameters are $username and $password and it generates a token if $username and $password are matched with "username" and "password". The authorize() finds a $token from query string and return payload if $token is valid. We don't use Authorization header in request but the query string so that we can use the token in Url.
    * UserServiceInterface.php
    ```php
    namespace App\Services;

    interface UserServiceInterface
    {
        public function login($username, $password);
        public function authorize();
    }
    ```
    * UserService.php
    ```php
    namespace App\Services;

    use Psr\Http\Message\ServerRequestInterface;
    use Firebase\JWT\JWT;

    class UserService implements UserServiceInterface
    {
        private const key = "secret key";
        private $request;

        public function __construct(ServerRequestInterface $request)
        {
            $this->request = $request;
        }

        public function login($username, $password)
        {
            if (!is_string($username) || $username !== "username")
                throw new \Exception("Invalid username", 400);
            if (!is_string($password) || $password !== "password")
                throw new \Exception("Invalid password", 400);
            $time = time();
            $payload =
            [
                'iss' => "PHP-JWT",
                'iat' => $time,
                'nbf' => $time + 10,
                'exp' => $time + 600,
                'user' => $username,
            ];
            return JWT::encode($payload, $this::key);
        }

        public function authorize()
        {
            $query = $this->request->getQueryParams();
            $token = "";
            if (isset($query["token"]))
                $token = $query["token"];
            try
            {
                return JWT::decode($token, $this::key, array("HS256"));
            }
            catch (\Throwable $e)
            {
                throw new \Exception($e->getMessage(), 403);
            }
        }
    }
    ```
3. We also create a controller has 2 action methods login() and getUserInfo(). The login() method get 2 parameters $username and $password from the Url path rather then from POST data so we can test it in a browser easily. The other getUserInfo() method need to be authorized and print the payload from the valid token.
    * UserController.php
    ```php
    namespace App\Controllers;

    use PHPWebCore\Controller;
    use App\Services\UserServiceInterface;

    class UserController extends Controller
    {
        private $userService;

        public function __construct(UserServiceInterface $userService)
        {
            $this->userService = $userService;
        }

        public function login($username, $password)
        {
            $token = $this->userService->login($username, $password);
            echo "token = {$token}";
        }

        public function getUserInfo()
        {
            if (isset($this->bucket["user"]))
                echo json_encode($this->bucket["user"]);
            header("Content-Type: application/json");
        }
    }
    ```
4. Now we just need to declare routes and configure app entry point then it's done.
    * Route.php
    ```php
    namespace App;

    use PHPWebCore\AppRoute;
    use PHPWebCore\RouteProperty;
    use PHPWebCore\HttpMethod;
    use App\Controllers\UserController;

    class Route extends AppRoute
    {
        public function initialize()
        {
            $this->routes = 
            [
                [
                    // Root path can be empty or "/"
                    RouteProperty::Path => "login",
                    // HTTP method attached to this action. If no declaration then all methods are accepted
                    RouteProperty::Methods => [HttpMethod::Get],
                    // Parameters is an a array of string, contains all parameters' names
                    RouteProperty::Controller => UserController::class,
                    // Parameters is an a array of string, contains all parameters' names
                    RouteProperty::Parameters => ["username", "password"],
                    // Action method name
                    RouteProperty::Action => "login",
                ],
                [
                    // Root path can be empty or "/"
                    RouteProperty::Path => "user",
                    // HTTP method attached to this action. If no declaration then all methods are accepted
                    RouteProperty::Methods => [HttpMethod::Get],
                    // Parameters is an a array of string, contains all parameters' names
                    RouteProperty::Controller => UserController::class,
                    // Action method name
                    RouteProperty::Action => "getUserInfo",
                    // If true, this action need to be authorized
                    RouteProperty::Authorized => true,
                ],
            ];
        }
    }
    ```
    * Bootstrap.php
    ```php
    namespace App;

    use Psr\Http\Message\ServerRequestInterface;
    use PHPWebCore\App;
    use PHPWebCore\RouteProperty;
    use App\Services\UserServiceInterface;
    use App\Services\UserService;
    
    class Bootstrap extends App
    {
        public function process()
        {
            // Add services to container
            $this->container = $this->container
                ->withSingleton(ServerRequestInterface::class, $this->request)
                ->withTransient(UserServiceInterface::class, UserService::class);

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

            // Invoke the action to fulfill the request
            // Data likes user information from Authorization can be passed to controller by bucket
            $this->invokeAction(bucket: $bucket);
        }
    }
    ```
5. Run your PHPWebCore app and use the login Url to get a token. Then, we use the token in user Url to get the payload from the token.
    * http://[your host]/login/username/password
    * http://[your host]/user?token=[token]
