<?php
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
?>