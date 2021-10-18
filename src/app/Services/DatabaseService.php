<?php
namespace App\Services;

use RedBeanPHP\R;

class DatabaseService
{
    public const Project = "porject";
    public const ProjectName = "name";
    public const ProjectFramework = "framework";

    public function __construct(string $connectionString)
    {
        // Open a connaction
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
?>