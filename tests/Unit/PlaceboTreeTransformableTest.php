<?php

namespace Tests\Unit;

use Jlvn\TreeTransform\Map;
use Jlvn\TreeTransform\PlaceboTreeTransformable;
use Jlvn\TreeTransform\TreeTransformableInterface;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @covers \Jlvn\TreeTransform\PlaceboTreeTransformable
 */
class PlaceboTreeTransformableTest extends TestCase
{
    private TreeTransformableInterface $transformable;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transformable = new PlaceboTreeTransformable();
    }

    /** @test */
    public function it_returns_a_empty_array_on_get_branches(): void {
        $this->assertEquals([], $this->transformable->getBranches(null));
    }

    /** @test */
    public function it_returns_mixed_on_get_type(): void {
        $this->assertEquals('mixed', $this->transformable->getTag());
    }

    /** @test */
    public function it_return_a_identical_object_on_transform(): void {
        $trunk = new stdClass;
        $this->assertEquals($trunk, $this->transformable->transform($trunk, new Map));
    }
}