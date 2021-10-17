<?php
namespace PHPCore;

class Container implements ContainerInterface
{
    protected $container = [];

    public function withSingleton(string $id, string|object $entry, $parameters = [])
    {
        return $this->add(EntryLifetime::Singleton, $id, $entry, $parameters);
    }

    public function withTransient(string $id, string $entry, $parameters = [])
    {
        return $this->add(EntryLifetime::Transient, $id, $entry, $parameters);
    }

    public function get(string $id)
    {
        if (!$this->has($id))
            throw new NotFoundException("{$id} not found in container");
        $entry = $this->container[$id];
        if ($entry["lifetime"] === EntryLifetime::Singleton)
        {
            if (!isset($entry["instance"]))
                $entry["instance"] = $this->resolve($entry["class"], $entry["parameters"]);
            return $entry["instance"];
        }
        else
        {
            if (isset($entry["class"]))
                return $this->resolve($entry["class"], $entry["parameters"]);
            else
            {
                if (is_object($entry["instance"]))
                    return clone $entry["instance"];
                else if (is_array($entry["instance"]))
                    return array_merge(array(), $entry["instance"]);
                else
                {
                    $clone = $entry["instance"];
                    return $clone;
                }
            }
        }
    }

    public function has(string $id)
    {
        if (!is_string($id))
            throw new \InvalidArgumentException("Id must be a string");
        return isset($this->container[$id]);
    }

    protected function add($lifetime, string $id, string|object $entry, $parameters = [])
    {
        if (!is_string($id))
            throw new \InvalidArgumentException("Id must be a string");
        $id = trim($id);
        if (empty($id))
            throw new \InvalidArgumentException("Id must not be empty");
        if (!is_string($entry) && !is_object($entry))
            throw new \InvalidArgumentException("Entry must be a string or an object");
        if (!is_array($parameters))
            $parameters = [$parameters];
        $clone = clone $this;
        if (is_string($entry) && class_exists($entry))
        {
            $clone->container[$id] = array
            (
                "lifetime" => $lifetime,
                "class" => $entry,
                "parameters" => $parameters,
            );
        }
        else
        {
            if (is_null($entry))
                throw new \InvalidArgumentException("Entry must not be null");
            $clone->container[$id] = array
            (
                "lifetime" => $lifetime,
                "instance" => $entry,
                "parameters" => $parameters,
            );
        }
        return $clone;
    }

    protected function resolve(string $class, $parameters)
    {
        if ($class instanceof \Closure)
            return call_user_func_array($class, $parameters);
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
            $name = $param->getName();
            if (isset($parameters[$name]))
                $dependencies[] = $parameters[$name];
            else
            {
                $type = $param->getType();
                if (is_null($type))
                {
                    if ($param->isDefaultValueAvailable())
                        $dependencies[] = $param->getDefaultValue();
                    else
                        throw new \Exception("Cannot resolve dependency {$param->__toString()}");
                } 
                else
                    $dependencies[] = ($this->get($type->getName()));
            }
        }
        return $reflector->newInstanceArgs($dependencies);
    }
}
?>