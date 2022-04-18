<?php

namespace Jlvn\TreeTransform;

/**
 * @implements WriteOnlyMapInterface<mixed>
 * @implements ReadOnlyMapInterface<mixed>
 */
class Map implements WriteOnlyMapInterface, ReadOnlyMapInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $map;

    /**
     * Instantiate a new map.
     */
    public function __construct()
    {
        $this->map = [];
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, mixed $value): void
    {
        $this->map[$key] = $value;
    }

    /**
     * @inheritDoc
     */
    public function tryGet(string $key): mixed
    {
        if (!isset($this->map[$key])) {
            throw new NotFoundException("key ($key) not found");
        }
        return $this->map[$key];
    }

    /**
     * @inheritDoc
     */
    public function getOrDefault(string $key, mixed $default = null): mixed
    {
        try {
            return $this->tryGet($key);
        } catch (NotFoundExceptionInterface) {
            return $default;
        }
    }
}