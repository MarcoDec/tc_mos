<?php

namespace App\Entity;

use Exception;
use ReflectionClass;
use ReflectionException;

class PropertyAccessor
{
    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public static function get($object, $propertyName)
    {
        $reflectionClass = new ReflectionClass($object);
        $property = $reflectionClass->getProperty($propertyName);
        $property->setAccessible(true);

        if (!$property->isInitialized($object)) {
            throw new Exception("The property $propertyName is not initialized in " . get_class($object));
        }

        $getter = 'get' . ucfirst($propertyName);

        if (method_exists($object, $getter)) {
            return $object->$getter();
        }

        return $property->getValue($object);
    }
}