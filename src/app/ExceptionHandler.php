<?php
namespace App;

use PHPWebCore\ErrorServiceInterface;

class ExceptionHandler implements ErrorServiceInterface
{
    public function process(\Throwable $e)
    {
        echo "I've got the exception for now.";
    }
}
?>