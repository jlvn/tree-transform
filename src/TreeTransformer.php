<?php

namespace Jlvn\TreeTransform;

use Closure;

class TreeTransformer
{
    private readonly TreeTransformableTagReadOnlyMap $defaultTransformableMap;
    private readonly TreeTransformableInterface $defaultTransformable;

    /**
     * @param TreeTransformableInterface $defaultTransformable The default transformable to transform entities with.
     *        if null is passed the PlaceboTreeTransformable will be used.
     *        This transformable does not transform the entity.
     * @param TreeTransformableTagReadOnlyMap $defaultTransformableMap The default transformable map to transform the
     *        the entities in the tree with. If null is passed an empty TreeTransformableTagReadOnlyMap will be used.
     *
     * @see PlaceboTreeTransformable
     * @see TreeTransformableTagReadOnlyMap
     */
    public function __construct(
        TreeTransformableInterface $defaultTransformable = new PlaceboTreeTransformable(),
        TreeTransformableTagReadOnlyMap $defaultTransformableMap = new TreeTransformableTagReadOnlyMap([])
    )
    {
        $this->defaultTransformable = $defaultTransformable;
        $this->defaultTransformableMap = $defaultTransformableMap;
    }

    /**
     * Transform an entity. If an entity tag can't be found in the transformable map an exception will be raised.
     *
     * @template T
     *
     * @param T $trunk The entity you wish to transform.
     * @param TreeTransformableTagReadOnlyMap|null $transformableMap the transformable map you want to use to transform
     *        this tree entity and its branches. If null is passed the default transformable map will be used.
     *
     * @return mixed
     *
     * @throws NotFoundExceptionInterface When the transformable tag could not be found in the transformable map.
     */
    public function tryTransform(mixed $trunk, TreeTransformableTagReadOnlyMap $transformableMap = null): mixed
    {
        return $this->transform(
            $trunk,
            $transformableMap ?? $this->defaultTransformableMap,
            fn(TreeTransformableTagReadOnlyMap $transformableMap, string $trunkTag): TreeTransformableInterface =>
            $transformableMap->tryGet($trunkTag)
        );
    }

    /**
     * Transform an entity. If an entity tag can't be found in the transformable map
     * the default or provided transformable will be used.
     *
     * @template T
     *
     * @param T $trunk The entity you wish to transform.
     * @param TreeTransformableTagReadOnlyMap|null $transformableMap the transformable map you want to use to transform
     *        this tree entity and its branches. If null is passed the default transformable map will be used.
     * @param TreeTransformableInterface|null $transformable the default transformable you want to use to transform
     *        this tree entity and its branches. If null is passed the default transformable will be used.
     *
     * @return mixed
     */
    public function transformOrDefault(
        mixed $trunk,
        TreeTransformableTagReadOnlyMap $transformableMap = null,
        TreeTransformableInterface $transformable = null
    ): mixed
    {
        return $this->transform(
            $trunk,
            $transformableMap ?? $this->defaultTransformableMap,
            fn(TreeTransformableTagReadOnlyMap $transformableMap, string $trunkTag): TreeTransformableInterface =>
                $transformableMap->getOrDefault($trunkTag, $transformable ?? $this->defaultTransformable)
        );
    }

    /**
     * @param mixed $trunk
     * @param TreeTransformableTagReadOnlyMap $transformableMap
     * @param Closure $getTransformable
     * @return mixed
     */
    private function transform(
        mixed $trunk,
        TreeTransformableTagReadOnlyMap $transformableMap,
        Closure $getTransformable
    ): mixed
    {
        $trunkTag = $this->getTag($trunk);
        $trunkTransformable = $getTransformable($transformableMap, $trunkTag);
        $transformedMap = new Map;

        $branches = $trunkTransformable->getBranches($trunk);

        foreach ($branches as $branch) {
            $branchTag = $this->getTag($branch);
            $transformedMap->set($branchTag, [
                    ...$transformedMap->getOrDefault($branchTag, []),
                    $this->transform($branch, $transformableMap, $getTransformable)
                ]
            );
        }

        return $trunkTransformable->transform($trunk, $transformedMap);
    }

    /**
     * Get the typing or class of a mixed value.
     * @param mixed $value
     * @return string
     */
    private function getTag(mixed $value): string
    {
        $tag = gettype($value);
        if ($tag !== 'object') {
            return $tag;
        }
        return $value::class;
    }
}