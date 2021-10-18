<?php
namespace App\Controllers;

use App\Services\ProjectServiceInterface;
use PHPWebCore\Controller;

class ProjectController extends Controller
{
    private $projectService;
    public function __construct(ProjectServiceInterface $projectService)
    {
        $this->projectService = $projectService;
    }

    public function getProjectInfo()
    {
        // return json
        echo $this->projectService->getProjectInfo();
        // set content type is application/json
        header("Content-Type: application/json");
    }
}
?>