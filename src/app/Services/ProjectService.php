<?php
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
?>