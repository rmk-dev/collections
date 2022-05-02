<?php

namespace Rmk\Collections;

use Rmk\Collections\Exception\InvalidValueTypeException;

/**
 * Abstract class for collections with object of the same class
 */
abstract class AbstractClassCollection extends Collection
{

    /**
     * Overrides the default method to ensure the value is instance of the required class
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return void
     *
     * @throws InvalidValueTypeException
     */
    public function offsetSet($key, $value)
    {
        $this->ensureValidValue($value);
        parent::offsetSet($key, $value);
    }

    /**
     * @param mixed $value
     *
     * @return void
     *
     * @throws InvalidValueTypeException
     */
    protected function ensureValidValue($value)
    {
        $className = $this->getClassName();
        if (!($value instanceof $className)) {
            throw new InvalidValueTypeException('Value must be instance of ' . $className);
        }
    }

    /**
     * The class that the object must instance of
     *
     * @return string
     */
    abstract public function getClassName(): string;
}