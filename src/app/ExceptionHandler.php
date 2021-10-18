<?php
namespace App;

use PHPWebCore\ErrorServiceInterface;

class ExceptionHandler implements ErrorServiceInterface
{
    public function process(\Throwable $e)
    {
        echo "This is not an exception. It is testing";
        exit;
    }
}
?>