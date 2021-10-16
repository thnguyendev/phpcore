<?php
namespace PHPCore;

interface ContainerInterface
{
    public function withSingleton(string $id, string|object $entry, $parameters = []);
    public function withTransient(string $id, string $entry, $parameters = []);
    public function get(string $id);
    public function has(string $id);
}
?>