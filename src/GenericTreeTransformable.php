<?php

namespace Jlvn\TreeTransform;

use Closure;

class GenericTreeTransformable implements TreeTransformableInterface
{
    private string $type;
    private Closure $getBranches;
    private Closure $transform;

    /**
     * @param string $type
     * @param Closure(mixed): array $getBranches
     * @param Closure(mixed, ReadOnlyMapInterface): mixed $transform
     */
    public function __construct(string $type, Closure $getBranches, Closure $transform)
    {
        $this->type = $type;
        $this->getBranches = $getBranches;
        $this->transform = $transform;
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return $this->type;
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