<?php

namespace Jlvn\TreeTransform;

use Closure;

class TreeTransformer
{
    private readonly TreeTransformableReadOnlyMap $defaultTransformableMap;
    private readonly TreeTransformableInterface $defaultTransformable;

    /**
     * @param TreeTransformableInterface $defaultTransformable
     * @param TreeTransformableReadOnlyMap $defaultTransformableMap
     */
    public function __construct(
        TreeTransformableInterface $defaultTransformable = new PlaceboTreeTransformable(),
        TreeTransformableReadOnlyMap $defaultTransformableMap = new TreeTransformableReadOnlyMap([])
    )
    {
        $this->defaultTransformable = $defaultTransformable;
        $this->defaultTransformableMap = $defaultTransformableMap;
    }

    /**
     * @template T
     *
     * @param T $trunk
     * @param TreeTransformableReadOnlyMap $transformableMap
     *
     * @return mixed
     *
     * @throws NotFoundException
     */
    public function tryTransformWith(mixed $trunk, TreeTransformableReadOnlyMap $transformableMap): mixed
    {
        return $this->transform(
            $trunk,
            $transformableMap,
            fn(TreeTransformableReadOnlyMap $transformableMap, string $trunkType): TreeTransformableInterface =>
            $transformableMap->tryGet($trunkType)
        );
    }

    /**
     * @template T
     *
     * @param T $trunk
     *
     * @return mixed
     *
     * @throws NotFoundException
     */
    public function tryTransform(mixed $trunk): mixed
    {
        return $this->transform(
            $trunk,
            $this->defaultTransformableMap,
            fn(TreeTransformableReadOnlyMap $transformableMap, string $trunkType): TreeTransformableInterface =>
                $transformableMap->tryGet($trunkType)
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
            fn(TreeTransformableReadOnlyMap $transformableMap, string $trunkType): TreeTransformableInterface =>
                $transformableMap->getOrDefault($trunkType, $this->defaultTransformable)
        );
    }

    /**
     * @template T
     *
     * @param T $trunk
     * @param TreeTransformableReadOnlyMap $transformableMap
     * @return mixed
     */
    public function transformOrDefaultWith(
        mixed $trunk,
        TreeTransformableReadOnlyMap $transformableMap
    ): mixed
    {
        return $this->transform(
            $trunk,
            $transformableMap,
            fn(TreeTransformableReadOnlyMap $transformableMap, string $trunkType): TreeTransformableInterface =>
                $transformableMap->getOrDefault($trunkType, $this->defaultTransformable)
         );
    }

    /**
     * @param mixed $trunk
     * @param TreeTransformableReadOnlyMap $transformableMap
     * @param Closure $getTransformable
     * @return mixed
     */
    private function transform(mixed $trunk, TreeTransformableReadOnlyMap $transformableMap, Closure $getTransformable): mixed
    {
        $trunkType = $this->getType($trunk);
        $trunkTransformable = $getTransformable($transformableMap, $trunkType);
        $transformedMap = new Map;

        $branches = $trunkTransformable->getBranches($trunk);

        foreach ($branches as $branch) {
            $branchType = $this->getType($branch);
            $transformedMap->set($branchType, [
                    ...$transformedMap->getOrDefault($branchType, []),
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
    private function getType(mixed $value): string
    {
        $type = gettype($value);
        if ($type !== 'object') {
            return $type;
        }
        return $value::class;
    }
}