<?php

namespace App;

use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

trait PrivateInheritanceTrait {
    /**
     * @throws ReflectionException
     *
     * @return mixed|void
     */
    public function __get(string $name) {
        if (in_array($name, $this->getFields())) {
            return $this->getProperty($name)->getValue($this);
        }
    }

    /**
     * @param mixed $value
     *
     * @throws ReflectionException
     */
    public function __set(string $name, $value): void {
        if (in_array($name, $this->getFields())) {
            $this->getProperty($name)->setValue($this, $value);
        }
    }

    /**
     * @return string[]
     */
    abstract protected function getFields(): array;

    /**
     * @throws ReflectionException
     */
    private function getProperty(string $name): ReflectionProperty {
        $property = (new ReflectionClass(parent::class))->getProperty($name);
        $property->setAccessible(true);
        return $property;
    }
}
