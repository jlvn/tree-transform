<?php

namespace Jlvn\TreeTransform;

/**
 * @template TTrunk
 * @template TBranch
 * @template TTransformed
 */
interface TreeTransformableInterface {

    /**
     * Get the tag of the transformable.
     * For primitives, it should be the result of the gettype() function.
     * For objects, it should be the fully qualified name of the class.
     * E.G. the result of object::class.
     *
     * @see gettype()
     * @see get_class()
     * @see object::class
     *
     * @return string
     */
    public function getTag(): string;

    /**
     * Get the next level of branches of this trunk.
     * This can be a mixed array.
     *
     * @param TTrunk $trunk a trunk you want to get the branches from
     *
     * @return array<TBranch>
     */
    public function getBranches(mixed $trunk): array;


    /**
     * Transform one data structure into another.
     *
     * @param TTrunk $trunk a trunk that you want to transform.
     * @param ReadOnlyMapInterface $branches The generated children branch map of this trunk.
     *
     * @return TTransformed the transformed object.
     */
    public function transform(mixed $trunk, ReadOnlyMapInterface $branches): mixed;
}