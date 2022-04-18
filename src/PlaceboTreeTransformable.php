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
    public function getTag(): string
    {
        return 'mixed';
    }

    /**
     * @inheritDoc
     */
    public function getBranches($trunk): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function transform($trunk, ReadOnlyMapInterface $branches)
    {
        return $trunk;
    }
}