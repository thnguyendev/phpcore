<?php
// Interface definition
abstract class Animal {
  abstract public function makeSound();
}

// Class definitions
class Cat extends Animal {
  public function makeSound() {
    echo " Meow ";
  }
}

class Dog extends Animal {
  public function makeSound() {
    echo " Bark ";
  }
}

class Mouse {
  public function makeSound() {
    echo " Squeak ";
  }
}

class Test
{
  public $animal;
  public function __construct(Animal $animal)
  {
    $this->animal = $animal;
  }
}

// Create a list of animals
$cat = new Cat();
$dog = new Dog();
$mouse = new Mouse();

$str = "{123}";
$length = strlen($str);
echo "{$str[0]} | {$str[$length - 1]}";
?>