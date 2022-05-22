<?php

namespace Rmk\Collections;

use Rmk\Collections\Exception\InvalidValueTypeException;

/**
 * Collection of objects that are instance of specific class
 */
class BaseClassCollection extends AbstractClassCollection
{
    /**
     * @var string
     */
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
        $this->className = $className;
        parent::__construct($data, $flags, $iteratorClass);
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }
}
