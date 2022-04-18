<?php

namespace Jlvn\TreeTransform;

/**
 * @template T
 */
interface ReadOnlyMapInterface
{
    /**
     * Try to get the value for a key throws if key is not present in the map.
     *
     * @param string $key The key you want to lookup.
     *
     * @throws NotFoundExceptionInterface an exception when the key is not found.
     *
     * @return T the value.
     */
    public function tryGet(string $key): mixed;

    /**
     * Get the value for the key if present else get a default value.
     *
     * @param string $key The key you want to lookup.
     * @param mixed|null|T $default The default value if the key is not present.
     *
     * @return T the (default) value.
     */
    public function getOrDefault(string $key, mixed $default = null): mixed;
}