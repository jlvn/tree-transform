<?php

namespace Jlvn\TreeTransform;

/**
 * @template T
 */
interface WriteOnlyMapInterface
{
    /**
     * Map a key to a value.
     * @param string $key A key.
     * @param T $value A value.
     * @return void
     */
    public function set(string $key, $value): void;
}