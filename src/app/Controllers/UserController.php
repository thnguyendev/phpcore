<?php
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
?>