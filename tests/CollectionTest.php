<?php

namespace RmkTests\Collections;

use PHPUnit\Framework\TestCase;
use Rmk\Collections\Exception\UndefinedCollectionKeyException;
use Rmk\Collections\Collection;

class CollectionTest extends TestCase
{

    public function testMainMethods(): void
    {
        $collection = new Collection([1, 2, 3]);
        $this->assertTrue($collection->has(0));
        $this->assertTrue($collection->has(1));
        $this->assertTrue($collection->has(2));
        $collection->set('test', 'value');
        $this->assertTrue($collection->has('test'));
        $this->assertTrue($collection->contains('value'));
        $this->assertEquals('value', $collection->get('test'));
        $this->assertEquals('test_create', $collection->getOrCreate('created', 'test_create'));
        $this->assertEquals('test_create', $collection->getOrCreate('created', 'second try to create'));
        $this->expectException(UndefinedCollectionKeyException::class);
        $this->expectExceptionMessage('No unknown key in the collection');
        $collection->get('unknown');
    }

    public function testCreateNewCollectionsAndTraverseData(): void
    {
        $collection = new Collection([
            'one' => 1,
            'two' => 2,
            'three' => 3,
            'four' => 4,
        ]);
        $mapped = $collection->map(function($a) { return $a*$a; });
        $this->assertEquals($collection->count(), $mapped->count());
        $this->assertEquals(4, $mapped->get('two'));
        $this->assertEquals(9, $mapped->get('three'));
        $filtered = $collection->filter(function($a) { return !($a % 2); });
        $this->assertEquals(2, $filtered->count());
        $this->assertTrue($filtered->has('two'));
        $this->assertTrue($filtered->has('four'));
        $this->assertFalse($filtered->has('one'));
        $collection->set('duplicated', 2);
        $uniques = $collection->uniques();
        $this->assertNotEquals($collection->count(), $uniques->count());
        $this->assertEquals(4, $uniques->count());
        $this->assertTrue($uniques->has('two'));
        $this->assertFalse($uniques->has('duplicated'));
        $collection->apply(function($a) { return $a * 2; });
        $this->assertEquals(4, $collection->get('two'));
        $this->assertEquals(6, $collection->get('three'));
        $this->assertEquals(8, $collection->get('four'));
        $sliced = $collection->slice(0, 2);
        $this->assertEquals(2, $sliced->count());
        $this->assertTrue($sliced->has('one'));
        $this->assertTrue($sliced->has('two'));
        $this->assertEquals(2, $sliced->get('one'));
        $this->assertEquals(4, $sliced->get('two'));
        $this->assertFalse($sliced->has('three'));
        $sliced2 = $collection->slice(2);
        $this->assertEquals(3, $sliced2->count());
        $this->assertTrue($sliced2->has('three'));
        $this->assertTrue($sliced2->has('four'));
        $this->assertTrue($sliced2->has('duplicated'));
        $this->assertEquals(6, $sliced2->get('three'));
        $this->assertEquals(8, $sliced2->get('four'));
    }

    public function testJsonSerialize(): void
    {
        $values = ['a' => 1];
        $collection = new Collection($values);
        $this->assertEquals(json_encode($values), json_encode($collection));
    }
}