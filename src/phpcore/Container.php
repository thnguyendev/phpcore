<?php
namespace PHPCore;

use InvalidArgumentException;

class Container implements ContainerInterface
{
    protected $container = [];

    public function withSingleton(string $id, string|object $entry, $parameters = [])
    {
        if (!is_string($id))
            throw new InvalidArgumentException("Id must be a string");
        $id = trim($id);
        if (empty($id))
            throw new InvalidArgumentException("Id must not be empty");
        if (!is_string($entry) && !is_object($entry))
            throw new InvalidArgumentException("Entry must be a string or an object");
        if (!is_array($parameters))
            $parameters = [$parameters];
        $clone = clone $this;
        if (is_string($entry))
        {
            $entry = trim($entry);
            if (!class_exists($entry))
                throw new InvalidArgumentException("{$entry} class has not been defined");
            $clone->container[$id] = array
            (
                "lifetime" => EntryLifetime::Singleton,
                "class" => $entry,
                "parameters" => $parameters,
            );
        }
        else
        {
            if (is_null($entry))
                throw new InvalidArgumentException("Entry must not be null");
            $clone->container[$id] = array
            (
                "lifetime" => EntryLifetime::Singleton,
                "instance" => $entry,
                "parameters" => $parameters,
            );
        }
        return $clone;
    }

    public function withTransient(string $id, string $entry, $parameters = [])
    {
        if (!is_string($id))
            throw new InvalidArgumentException("Id must be a string");
        $id = trim($id);
        if (empty($id))
            throw new InvalidArgumentException("Id must not be empty");
        if (!is_string($entry))
            throw new InvalidArgumentException("Entry must be a string");
        $entry = trim($entry);
        if (!class_exists($entry))
            throw new InvalidArgumentException("{$entry} class has not been defined");
        if (!is_array($parameters))
            $parameters = [$parameters];
        $clone = clone $this;
        $clone->container[$id] = array
        (
            "lifetime" => EntryLifetime::Transient,
            "class" => $entry,
            "parameters" => $parameters,
        );
        return $clone;
    }

    public function get(string $id)
    {
        if (!$this->has($id))
            throw new NotFoundException("{$id} not found");
        $entry = $this->container[$id];
        if ($entry["lifetime"] === EntryLifetime::Singleton)
        {
            if (!isset($entry["instance"]))
                $entry["instance"] = $this->resolve($entry["class"], $entry["parameters"]);
            return $entry["instance"];
        }
        else
            return $this->resolve($entry["class"], $entry["parameters"]);
    }

    public function has(string $id)
    {
        if (!is_string($id))
            throw new InvalidArgumentException("Id must be a string");
        $id = trim($id);
        return isset($this->container[$id]);
    }

    protected function resolve(string $class, $parameters)
    {
        if ($class instanceof \Closure) {
            return $class($this, $parameters);
        }
        $reflector = new \ReflectionClass($class);
        if (!$reflector->isInstantiable())
            throw new \Exception("Class {$class} is not instantiable");
        $constructor = $reflector->getConstructor();
        if (is_null($constructor))
            return $reflector->newInstance();
        $params = $constructor->getParameters();
        $dependencies = [];
        foreach ($params as $param)
        {
            $dependency = $param->getType();
            if (is_null($dependency))
            {
                if (count($parameters) > 0)
                {
                    $dependencies[] = $parameters[0];
                    $parameters = array_slice($parameters, 1);
                }
                else
                {
                    if ($param->isDefaultValueAvailable())
                        $dependencies[] = $param->getDefaultValue();
                    else
                        throw new \Exception("Cannot resolve dependency {$param->__toString()}");
                }
            } 
            else
                $dependencies[] = ($this->get($dependency->getName()));
        }
        return $reflector->newInstanceArgs($dependencies);
    }
}
?>