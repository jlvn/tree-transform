<?php

use Jlvn\TreeTransform\GenericTreeTransformable;
use Jlvn\TreeTransform\ReadOnlyMapInterface;
use Classes\Dog;
use Jlvn\TreeTransform\NotFoundException;
use Jlvn\TreeTransform\TreeTransformableInterface;
use Jlvn\TreeTransform\TreeTransformableTagReadOnlyMap;
use Jlvn\TreeTransform\TreeTransformer;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @covers \Jlvn\TreeTransform\TreeTransformer
 */
class TreeTransformerTest extends TestCase
{
    private TreeTransformableInterface $reflectionClassTransformable;
    private TreeTransformableInterface $reflectionMethodTransformable;
    private TreeTransformableInterface $reflectionParameterTransformable;
    private array $expectedTransformationResult;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reflectionClassTransformable = new GenericTreeTransformable(
            ReflectionClass::class,
            fn(ReflectionClass $trunk) => $trunk->getMethods(ReflectionMethod::IS_PUBLIC),
            fn(ReflectionClass $trunk, ReadOnlyMapInterface $branches) => [
                'name' => $trunk->getName(),
                'methods' => $branches->getOrDefault(ReflectionMethod::class, [])
            ]
        );

        $this->reflectionMethodTransformable = new GenericTreeTransformable(
            ReflectionMethod::class,
            fn(ReflectionMethod $trunk) => $trunk->getParameters(),
            fn(ReflectionMethod $trunk, ReadOnlyMapInterface $branches) => [
                'name' => $trunk->getName(),
                'parameters' => $branches->getOrDefault(ReflectionParameter::class, [])
            ]
        );

        $this->reflectionParameterTransformable = new GenericTreeTransformable(
            ReflectionParameter::class,
            fn() => [],
            fn(ReflectionParameter $trunk) => [
                'name' => $trunk->getName(),
            ],
        );

        $this->expectedTransformationResult = [
            'name' => 'Classes\Dog',
            'methods' => [
                [
                    'name' => 'eat',
                    'parameters' => [
                        [
                            'name' => 'food'
                        ]
                    ]
                ]
            ]
        ];
    }

    /** @test */
    public function it_will_throw_a_exception_when_the_try_method_is_used(): void
    {
        $this->expectException(NotFoundException::class);

        $treeTransformer = new TreeTransformer;

        $treeTransformer->tryTransform(new StdClass);
    }

    /** @test
     * @throws NotFoundException
     */
    public function it_will_transform_a_object_with_default_transformable_map(): void
    {
        $transformableMap = new TreeTransformableTagReadOnlyMap([
            $this->reflectionClassTransformable,
            $this->reflectionMethodTransformable,
            $this->reflectionParameterTransformable,
        ]);

        $treeTransformer = new TreeTransformer(defaultTransformableMap: $transformableMap);

        $reflected = new ReflectionClass(Dog::class);

        $actual = $treeTransformer->tryTransform($reflected);

        $this->assertEquals($this->expectedTransformationResult, $actual);
    }

    /** @test
     * @throws NotFoundException
     */
    public function it_will_transform_a_object_with_provided_transformable_map(): void
    {
        $transformableMap = new TreeTransformableTagReadOnlyMap([
            $this->reflectionClassTransformable,
            $this->reflectionMethodTransformable,
            $this->reflectionParameterTransformable,
        ]);

        $treeTransformer = new TreeTransformer;

        $reflected = new ReflectionClass(Dog::class);

        $actual = $treeTransformer->tryTransform($reflected, $transformableMap);

        $this->assertEquals($this->expectedTransformationResult, $actual);
    }

    /** @test */
    public function it_will_return_a_identical_object_with_default_transform(): void
    {
        $treeTransformer = new TreeTransformer;

        $reflected = new ReflectionClass(Dog::class);

        $actual = $treeTransformer->transformOrDefault($reflected);

        $this->assertEquals($reflected, $actual);
    }

    /** @test
     * @throws ReflectionException
     */
    public function it_will_correctly_transform_using_transform_or_default_with(): void
    {
        $treeTransformer = new TreeTransformer;

        $reflected = new ReflectionClass(Dog::class);

        $transformableMap = new TreeTransformableTagReadOnlyMap([
            $this->reflectionClassTransformable,
            $this->reflectionMethodTransformable
        ]);

        $actual = $treeTransformer->transformOrDefault($reflected, $transformableMap);

        $expected =  [
            'name' => 'Classes\Dog',
            'methods' => [
                [
                    'name' => 'eat',
                    'parameters' => [
                        new ReflectionParameter([(new Dog), 'eat'], 'food')
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $actual);
    }
}