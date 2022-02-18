<?php

namespace RmkTests\Collections;

use PHPUnit\Framework\TestCase;
use Rmk\Collections\ClassCollection;
use Rmk\Collections\Exception\InvalidValueTypeException;

class ClassCollectionTest extends TestCase
{

    public function testAddingObjects(): void
    {
        $collection = new ClassCollection(\stdClass::class);
        $collection->append(new \stdClass());
        $collection->append(new \stdClass());
        $collection->append(new \stdClass());
        $this->assertEquals(3, $collection->count());
        $this->expectException(InvalidValueTypeException::class);
        $this->expectExceptionMessage('Value must be instance of ' . \stdClass::class);
        $collection->append(new class {});
    }
}