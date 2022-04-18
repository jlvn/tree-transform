<?php

namespace Jlvn\TreeTransform;

use Closure;

class GenericTreeTransformable implements TreeTransformableInterface
{
    private string $tag;
    private Closure $getBranches;
    private Closure $transform;

    /**
     * @param string $tag
     * @param Closure(mixed): array $getBranches
     * @param Closure(mixed, ReadOnlyMapInterface): mixed $transform
     */
    public function __construct(string $tag, Closure $getBranches, Closure $transform)
    {
        $this->tag = $tag;
        $this->getBranches = $getBranches;
        $this->transform = $transform;
    }

    /**
     * @inheritDoc
     */
    public function getTag(): string
    {
        return $this->tag;
    }

    /**
     * @inheritDoc
     */
    public function getBranches(mixed $trunk): array
    {
        return call_user_func($this->getBranches, $trunk);
    }

    /**
     * @inheritDoc
     */
    public function transform(mixed $trunk, ReadOnlyMapInterface $branches): mixed
    {
        return call_user_func($this->transform, $trunk, $branches);
    }
}