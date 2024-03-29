<?php

namespace Rmk\Collections;

use \ArrayObject;
use \JsonSerializable;
use Rmk\Collections\Exception\UndefinedCollectionKeyException;

class Collection extends ArrayObject implements JsonSerializable
{
    /**
     * @param mixed $key
     * @return false|mixed
     */
    public function get($key)
    {
        if ($this->offsetExists($key)) {
            return $this->offsetGet($key);
        }

        throw new UndefinedCollectionKeyException(sprintf('No %s key in the collection', $key));
    }

    /**
     * @param mixed $key
     * @return bool
     */
    public function has($key): bool
    {
        return $this->offsetExists($key);
    }

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function set($key, $value): void
    {
        $this->offsetSet($key, $value);
    }

    /**
     * Alias of "set()"
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return void
     */
    public function add($key, $value): void
    {
        $this->set($key, $value);
    }

    /**
     * Add all values from a collection
     *
     * @param iterable $collection
     *
     * @return void
     */
    public function addAll(iterable $collection): void
    {
        foreach ($collection as $key => $value) {
            $this->add($key, $value);
        }
    }

    /**
     * @param mixed $value
     * @param bool  $strict
     *
     * @return bool
     */
    public function contains($value, bool $strict = true): bool
    {
        return in_array($value, $this->getArrayCopy(), $strict);
    }

    /**
     * @param callable $fn
     * @return Collection
     */
    public function map(callable $fn): Collection
    {
        return new static(array_map($fn, $this->getArrayCopy()));
    }

    /**
     * @param callable $fn
     * @return Collection
     */
    public function filter(callable $fn): Collection
    {
        return new static(array_filter($this->getArrayCopy(), $fn, ARRAY_FILTER_USE_BOTH));
    }

    /**
     * @return Collection
     */
    public function uniques(): Collection
    {
        return new static(array_unique($this->getArrayCopy()));
    }

    /**
     * @param int $offset
     * @param int|null $length
     *
     * @return $this
     */
    public function slice(int $offset, ?int $length = null): Collection
    {
        return new static(array_slice($this->getArrayCopy(), $offset, $length));
    }

    /**
     * @param callable $fn
     */
    public function apply(callable $fn): void
    {
        foreach ($this->getArrayCopy() as $key => $value) {
            $this->offsetSet($key, call_user_func_array($fn, [$value, $key, $this]));
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return false|mixed
     */
    public function getOrCreate(string $key, $value = null)
    {
        if (!$this->offsetExists($key)) {
            $this->set($key, $value);
        }

        return $this->get($key);
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * @param $value
     *
     * @return void
     */
    public function remove($value): void
    {
        $filtered = $this->filter(function($a) use ($value) { return $a !== $value; });
        $this->exchangeArray($filtered->getArrayCopy());
    }

    /**
     * @param Collection $collection
     *
     * @return void
     */
    public function removeAll(Collection $collection): void
    {
        $filtered = $this->filter(function($a) use ($collection) { return !$collection->contains($a); });
        $this->exchangeArray($filtered->getArrayCopy());
    }

    /**
     * Remove values if they correspond to a predicate
     *
     * Traverses all collection data and passes each value to the predicate.
     * If the result is true, the value is removed. The removed data is returned as method result.
     *
     * @param callable $predicate
     *
     * @return Collection The removed values
     */
    public function removeIf(callable $predicate): Collection
    {
        $removed = new Collection();
        foreach ($this->getArrayCopy() as $key => $value) {
            if ($predicate($value)) {
                $removed->set($key, $value);
                $this->offsetUnset($key);
            }
        }

        return $removed;
    }

    /**
     * Remove all values and their keys
     *
     * @return void
     */
    public function clear(): void
    {
        $this->exchangeArray([]);
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize(): mixed
    {
        return $this->getArrayCopy();
    }
}
