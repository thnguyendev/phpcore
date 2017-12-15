# phpcore
PHP Core Framework is a simple and easy to use MVC framework for web application development. It has been developed for the ease of building web application in PHP with OOP and MVC framework. It could be useful for projects in education.

## Quick start
1. Download PHP Core and copy to your project folder
2. Configure web server
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
3. Create a controller
    * Create "controllers" folder in your project folder
    * Create "home.php" in "controllers" folder
    ```php
    <?php
        class HomeController extends Controller {
            public function Process() {
                require_once($this->Views->GetModule($this->Request->Controller));
            }
        }
    ?>
    ```
    * Declare new controller in module by modifying file "modules/controller.php"
    ```php
    <?php
        // PHP Core
        // Author: Hung Thanh Nguyen

        // define folder for all of controllers, the name ControllerFolder should not be changed
        define("ControllerFolder", "controllers/", true);

        // declare your mvc controllers here, this declaration work like routes
        $ControllerList = array (
            "" => array ("file" => "home.php", "class" => "HomeController"),
            "home" => array ("file" => "home.php", "class" => "HomeController")
        );
    ?>
    ```
4. Create a view
    * Create "views" folder in your project folder
    * Create "home.php" in "views" folder
    ```php
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
    * Declare new view in module by modifying file "modules/view.php"
    ```php
    <?php
        // PHP Core
        // Author: Hung Thanh Nguyen

        // define folder for all of views, the name ViewFolder should not be changed
        define("ViewFolder", "views/", true);

        // declare views here
        $ViewList = array (
            "" => array ("file" => "home.php"),
            "home" => array ("file" => "home.php")
        );
    ?>
    ```

## Web API
1. Download, copy PHP Core framework to your project folder. Configure your web server
2. Create a model
    * Create "models" folder in your project folder
    * Create "info.php" in "models" folder
    ```php
    <?php
        class Info {
            public $Name = "PHP Core";
            public $Author = "Hung Thanh Nguyen";
        }
    ?>
    ```
    * Declare new model in module by modifying file "modules/model.php"
    ```php
    <?php
        // PHP Core
        // Author: Hung Thanh Nguyen

        // define folder for all of models, the name ModelFolder should not be changed
        define("ModelFolder", "models/", true);

        // decalre models here
        $ModelList = array (
            "info" => array ("file" => "info.php")
        );
    ?>
    ```
3. Create an API controller
    * Create "controllers" folder in your project folder
    * Create "api" folder in "controllers" folder
    * Create "getinfo.php" in "api" folder
    ```php
    <?php
        require_once($Models->GetModule("info"));
        class GetInfoController extends ApiController {
            public function Process() {
                if (preg_match("/^GET$/", $this->Request->Method)) {
                    $this->Get();
                }
                else if (!preg_match("/^OPTIONS$/", $this->Request->Method)) {
                    HttpCodes::MethodNotAllowed();
                }
            }

            public function Get() {
                header("Content-Type: application/json");
                $response = new Info();
                echo(json_encode($response));
            }
        }
    ?>
    ```
    * Declare new API controller in module by modifying file "modules/api.php"
    ```php
    <?php
        <?php
        // PHP Core
        // Author: Hung Thanh Nguyen

        // define folder for all of api controllers, the name ApiFolder should not be changed
        define("ApiFolder", "controllers/api/", true);

        // declare api controllers here
        $ApiList = array (
            "getinfo" => array ("file" => "getinfo.php", "class" => "GetInfoController")
        );
    ?>
    ```
4. Retrieve data from new Web API by url: Your project URL/api/getinfo
