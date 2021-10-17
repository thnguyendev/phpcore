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
    var_dump($name, $legs);
    parent::__construct($legs);
    $this->name = $name;
  }
  public function makeSound() {
    echo " Bark ";
  }
}

require_once(sprintf("%s/../vendor/autoload.php", __DIR__));
$test = function($param1, $param2, $param3 = 3) {
  echo $param1.PHP_EOL.$param2.PHP_EOL.$param3;
};
call_user_func_array($test, [1, 2]);
$clone = clone $test;
var_dump(is_object($test));

$str = "joe";
$str2 = $str;  
$str2 .= " blo"; // you have cloned $str to $str2 by modifying $str2

echo $str.PHP_EOL.$str2; // would give you: joe


$container = new Container();
$container = $container->withTransient(Animal::class, Dog::class, ["name" => "kiki", 4]);
// Create a list of animals
$dog = $container->get(Animal::class);
var_dump($dog);

?>