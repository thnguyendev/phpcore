<?php
use PHPCore\Container;
// Interface definition
abstract class Animal {
  protected $legs;
  public function __construct($legs)
  {
    $this->legs = $legs;
  }
  abstract public function makeSound();
}

class Dog extends Animal {
  protected $name;
  public function __construct($name, $legs)
  {
    parent::__construct($legs);
    $this->name = $name;
  }
  public function makeSound() {
    echo " Bark ";
  }
}

require_once(sprintf("%s/../vendor/autoload.php", __DIR__));

$container = new Container();
$container = $container->withTransient(Animal::class, Dog::class, ["kiki", 4]);
// Create a list of animals
$dog = $container->get(Animal::class);
var_dump($dog);
?>