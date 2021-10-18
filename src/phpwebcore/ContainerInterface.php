<?php
namespace PHPWebCore;

interface ContainerInterface
{
    public function withSingleton(string $id, $entry, $parameters = []);
    public function withTransient(string $id, $entry, $parameters = []);
    public function get(string $id);
    public function has(string $id);
}
?>