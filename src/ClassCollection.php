<?php

namespace Rmk\Collections;

use Rmk\Collections\Exception\InvalidValueTypeException;

/**
 * Collection of objects that are instance of specific class
 */
class ClassCollection extends Collection
{

    protected string $className;

    /**
     * @param string $className
     * @param array $data
     * @param int $flags
     * @param string $iteratorClass
     */
    public function __construct(
        string $className,
        array $data = [],
        int $flags = 0,
        string $iteratorClass = \ArrayIterator::class
    ) {
        parent::__construct($data, $flags, $iteratorClass);
        $this->className = $className;
    }

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
        if (!($value instanceof $this->className)) {
            throw new InvalidValueTypeException('Value must be instance of ' . $this->className);
        }
    }
}
