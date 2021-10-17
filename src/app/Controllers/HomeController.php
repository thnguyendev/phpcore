<?php
namespace App\Controllers;

use PHPWebCore\Controller;

class HomeController extends Controller
{
    public function index(string $name = null)
    {
        $this->view(["name" => $name]);
    }
}
?>