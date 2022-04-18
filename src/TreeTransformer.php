<?php

namespace Jlvn\TreeTransform;

use Closure;

class TreeTransformer
{
    private readonly TreeTransformableTagReadOnlyMap $defaultTransformableMap;
    private readonly TreeTransformableInterface $defaultTransformable;

    /**
     * @param TreeTransformableInterface $defaultTransformable
     * @param TreeTransformableTagReadOnlyMap $defaultTransformableMap
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
     * @template T
     *
     * @param T $trunk
     * @param TreeTransformableTagReadOnlyMap $transformableMap
     *
     * @return mixed
     *
     * @throws NotFoundExceptionInterface
     */
    public function tryTransformWith(mixed $trunk, TreeTransformableTagReadOnlyMap $transformableMap): mixed
    {
        return $this->transform(
            $trunk,
            $transformableMap,
            fn(TreeTransformableTagReadOnlyMap $transformableMap, string $trunkTag): TreeTransformableInterface =>
            $transformableMap->tryGet($trunkTag)
        );
    }

    /**
     * @template T
     *
     * @param T $trunk
     *
     * @return mixed
     *
     * @throws NotFoundExceptionInterface
     */
    public function tryTransform(mixed $trunk): mixed
    {
        return $this->transform(
            $trunk,
            $this->defaultTransformableMap,
            fn(TreeTransformableTagReadOnlyMap $transformableMap, string $trunkTag): TreeTransformableInterface =>
                $transformableMap->tryGet($trunkTag)
        );
    }

    /**
     * @template T
     *
     * @param T $trunk
     *
     * @return mixed
     *
     */
    public function transformOrDefault(mixed $trunk): mixed
    {
        return $this->transform(
            $trunk,
            $this->defaultTransformableMap,
            fn(TreeTransformableTagReadOnlyMap $transformableMap, string $trunkTag): TreeTransformableInterface =>
                $transformableMap->getOrDefault($trunkTag, $this->defaultTransformable)
        );
    }

    /**
     * @template T
     *
     * @param T $trunk
     * @param TreeTransformableTagReadOnlyMap $transformableMap
     * @return mixed
     */
    public function transformOrDefaultWith(
        mixed $trunk,
        TreeTransformableTagReadOnlyMap $transformableMap
    ): mixed
    {
        return $this->transform(
            $trunk,
            $transformableMap,
            fn(TreeTransformableTagReadOnlyMap $transformableMap, string $trunkTag): TreeTransformableInterface =>
                $transformableMap->getOrDefault($trunkTag, $this->defaultTransformable)
         );
    }

    /**
     * @param mixed $trunk
     * @param TreeTransformableTagReadOnlyMap $transformableMap
     * @param Closure $getTransformable
     * @return mixed
     */
    private function transform(mixed $trunk, TreeTransformableTagReadOnlyMap $transformableMap, Closure $getTransformable): mixed
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