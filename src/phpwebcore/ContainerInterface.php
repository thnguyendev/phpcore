<?php
namespace PHPWebCore;

interface ContainerInterface
{
    /**
     * Return an instance with the specified entry.
     * Entry will create only 1 instance if a class
     *
     * @param string $id ID of entry
     * @param mixed $entry could be class name, an object or any value
     * @param array $parameters arguments used if entry is a class name
     * @return static
     */
    public function withSingleton(string $id, $entry, $parameters = []);

    /**
     * Return an instance with the specified entry.
     * Entry will create an instance evrytime it's retrieved if a class
     *
     * @param string $id ID of entry
     * @param mixed $entry could be class name, an object or any value
     * @param array $parameters arguments used if entry is a class name
     * @return static
     */
    public function withTransient(string $id, $entry, $parameters = []);

    /**
     * Get a specified entry.
     *
     * @param string $id ID of entry
     * @return mixed
     */
    public function get(string $id);

    /**
     * Check if specified entry exist in container.
     *
     * @param string $id ID of entry
     * @return bool
     */
    public function has(string $id);
}
?>