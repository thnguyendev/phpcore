<?php
    namespace PHPCore;

    use InvalidArgumentException;

    class Container implements ContainerInterface
    {
        private $container = [];

        public function withSingleton(string $id, string|object $entry)
        {
            if (!is_string($id))
                throw new InvalidArgumentException("Id must be a string");
            $id = trim($id);
            if (empty($id))
                throw new InvalidArgumentException("Id must not be empty");
            if (!is_string($entry) && !is_object($entry))
                throw new InvalidArgumentException("Entry must be a string or an object");
            $clone = clone $this;
            if (is_string($entry))
            {
                $entry = trim($entry);
                if (!class_exists($entry))
                    throw new InvalidArgumentException("{$entry} class has not been defined");
                $clone->container[$id] = array
                (
                    "lifetime" => EntryLifetime::Singleton,
                    "class" => $entry
                );
            }
            else
            {
                if (is_null($entry))
                    throw new InvalidArgumentException("Entry must not be null");
                $clone->container[$id] = array
                (
                    "lifetime" => EntryLifetime::Singleton,
                    "instance" => $entry
                );
            }
            return $clone;
        }

        public function withTransient(string $id, string $entry)
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
            $clone = clone $this;
            $clone->container[$id] = array
            (
                "lifetime" => EntryLifetime::Transient,
                "class" => $entry
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
                    $entry["instance"] = $this->resolve($entry["class"]);
                return $entry["instance"];
            }
            else
                return $this->resolve($entry["class"]);
        }

        public function has(string $id)
        {
            if (!is_string($id))
                throw new InvalidArgumentException("Id must be a string");
            $id = trim($id);
            return isset($this->container[$id]);
        }

        private function resolve(string $class)
        {
            $reflector = new \ReflectionClass($class);
            if (!$reflector->isInstantiable())
                throw new \Exception("Class {$class} is not instantiable");
            $constructor = $reflector->getConstructor();
            if (is_null($constructor))
                return $reflector->newInstance();
            $parameters = $constructor->getParameters();
            $dependencies = [];
            foreach ($parameters as $parameter)
            {
                $dependency = $parameter->getType();
                if (is_null($dependency))
                {
                    if ($parameter->isDefaultValueAvailable())
                        $dependencies[] = $parameter->getDefaultValue();
                    else
                        throw new \Exception("Can not resolve dependency {$parameter->name}");
                } 
                else
                    $dependencies[] = ($this->get($dependency->getName()));
            }
            return $reflector->newInstanceArgs($dependencies);
        }
    }
?>