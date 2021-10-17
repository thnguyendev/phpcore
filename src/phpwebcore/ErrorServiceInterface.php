<?php
namespace PHPWebCore;

interface ErrorServiceInterface
{
    public function process(\Throwable $e);
}
?>