<?php

namespace Jlvn\TreeTransform;

/**
 * @implements TreeTransformableInterface<mixed, mixed, mixed>
 */
class PlaceboTreeTransformable implements TreeTransformableInterface
{

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return 'mixed';
    }

    /**
     * @inheritDoc
     */
    public function getBranches(mixed $trunk): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function transform(mixed $trunk, ReadOnlyMapInterface $branches): mixed
    {
        return $trunk;
    }
}