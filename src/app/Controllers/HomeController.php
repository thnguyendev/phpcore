<?php
namespace App\Controllers;

use PHPCore\Controller;

class HomeController extends Controller
{
    public function index(string $name)
    {
        $this->view([$name]);
    }
}
?>