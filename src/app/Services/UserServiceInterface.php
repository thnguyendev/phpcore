<?php
namespace App\Services;

interface UserServiceInterface
{
    public function login($username, $password);
    public function authorize();
}
?>