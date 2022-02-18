<?php

namespace Rmk\Collectoions;

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
    public function set($key, $value)
    {
        $this->offsetSet($key, $value);
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
     * @param callable $fn
     */
    public function apply(callable $fn): void
    {
        $collection = $this->map($fn);
        $this->exchangeArray($collection->getArrayCopy());
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
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return $this->getArrayCopy();
    }
}