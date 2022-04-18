<?php

namespace Jlvn\TreeTransform;

class TreeTransformableTagReadOnlyMap implements ReadOnlyMapInterface
{
    /**
     * @var array<string, TreeTransformableInterface>
     */
    private array $transformables;

    /**
     * @param array $transformables
     */
    public function __construct(array $transformables)
    {
        $this->transformables = $this->mapTransformablesToTags($transformables);
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
    public function getOrDefault(string $key, $default = null): ?TreeTransformableInterface
    {
        try {
            return $this->tryGet($key);
        } catch (NotFoundExceptionInterface $exception) {
            return $default;
        }
    }

    /***
     * @param TreeTransformableInterface[] $transformables
     * @return array<string, TreeTransformableInterface>
     */
    private function mapTransformablesToTags(array $transformables): array
    {
        $map = [];

        foreach ($transformables as $transformable) {
            $map[$transformable->getTag()] = $transformable;
        }

        return $map;
    }
}