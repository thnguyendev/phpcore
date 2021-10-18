<?php
namespace App\Controllers;

use PHPWebCore\Controller;

class ProjectController extends Controller
{
    public function getProjectInfo()
    {
        // return json
        echo "{ 'Project': 'PHPWebCore Api Example', 'Framework': 'PHPWebCore' }";
        // set content type is application/json
        header("Content-Type: application/json");
    }
}
?>