<?php
use PHPUnit\Framework\TestCase;
use PHPWebCore\Container;

class ContainerTest extends TestCase
{
    public function testContainer()
    {
        $container = new Container();
        $container = $container
            ->withTransient(Engine::class, Engine::class)
            ->withTransient(Wheel::class, Wheel::class)
            ->withSingleton(Car::class, Car::class);
        $car = $container->get(Car::class);
        $this->assertInstanceOf(Car::class, $car);
    }
}

class Engine { }

class Wheel
{
    public $numberOfWheels;
    public function __construct($numberOfWheels = 4)
    {
        $this->numberOfWheels = $numberOfWheels;
    }
}

class Car
{
    public $engine;
    public $wheel;
    public function __construct(Engine $engine, Wheel $wheel)
    {
        $this->engine = $engine;
        $this->wheel = $wheel;
    }
}
?>