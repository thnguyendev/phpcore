<?php
namespace PHPWebCore;

abstract class Enum
{
    private function __construct()
    {
        throw new \BadFunctionCallException();
    }

    private function __clone()
    {
        throw new \BadFunctionCallException();
    }

    final public static function toArray()
    {
        return (new \ReflectionClass(static::class))->getConstants();
    }

    final public static function isValid($value)
    {
        return in_array($value, static::toArray());
    }
}
?>