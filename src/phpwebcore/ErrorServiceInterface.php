<?php
namespace PHPWebCore;

interface ErrorServiceInterface
{
    /**
     * Process the exception.
     *
     * @param Throwable $e exception throwed out.
     */
    public function process(\Throwable $e);
}
?>