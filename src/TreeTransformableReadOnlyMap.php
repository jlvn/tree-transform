<?php

namespace Jlvn\TreeTransform;

class TreeTransformableReadOnlyMap implements ReadOnlyMapInterface
{
    /**
     * @var array<string, TreeTransformableInterface>
     */
    private readonly array $transformables;

    /**
     * @param array $transformables
     */
    public function __construct(array $transformables)
    {
        $this->transformables = $this->mapTransformablesToTypes($transformables);
    }


    /**
     * @inheritDoc
     */
    public function tryGet(string $key): TreeTransformableInterface
    {
        if (!isset($this->transformables[$key])) {
            throw new NotFoundException("transformable not found for key: $key");
        }
        return $this->transformables[$key];
    }

    /**
     * @inheritDoc
     */
    public function getOrDefault(string $key, mixed $default = null): ?TreeTransformableInterface
    {
        try {
            return $this->tryGet($key);
        } catch (NotFoundException) {
            return $default;
        }
    }

    /***
     * @param TreeTransformableInterface[] $transformables
     * @return array<string, TreeTransformableInterface>
     */
    private function mapTransformablesToTypes(array $transformables): array
    {
        $map = [];

        foreach ($transformables as $transformable) {
            $map[$transformable->getType()] = $transformable;
        }

        return $map;
    }
}