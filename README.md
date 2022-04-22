# Tree Transform
A library to perform transformations on tree-like structures.

## Usage
This library exposes a main class called: `TreeTransformer`.
With this class a user can transform a tree-like structure with different data-types
on a data-type by data-type basis.
This is done using so-called `TreeTransformable`s.

### TreeTransformables
A TreeTransformable defines the transforming behaviour of a certain data-type.
All TreeTransformables must implement the `TreeTransformableInterface`. 

#### Using the GenericTreeTransformable class
This library exposes the `GenericTreeTransformable` class. With this class TreeTransformables can be
created on the fly without having to define a class for each data type.

```php
// A transformable that transforms a string into an array of strings
$stringTreeTransformable = new GenericTreeTransformable(
    gettype(''), // the tag of the transformable. 'string' in this case.
    fn() => [], // the branches of the current transformable. None in this case.
    fn(string $trunk) => split('', $trunk), // the transform function
)
```

#### implementing your own transformables
Another option is to simply implement the `TreeTransformableInterface` for your own specific data type.

```php
class Custom {
    public string $name;
    public array $children;
}

class CustomTreeTransformable implements TreeTransformableInterface {
    public function getTag(): string {
        return Custom::class
    }
    
    public function getBranches($trunk): array {
        return $trunk->children;
    }

    public function transform($trunk, ReadOnlyMapInterface $branches) {
        return [
            'name' => $trunk->name,      
            'customChildren' => $branches->getOrDefault(Custom::class, [])     
        ]
    }
}
```

### Using the TreeTransformer
Now that we have defined our `TreeTransformable`s we can use them in the TreeTransformer.
The TreeTransformer uses a simple depth first algorithm to traverse the tree.
Tou can interact with the TreeTransformer in a multitude of ways.

```php
$transformer = new TreeTransformer;

// Throws a NotFoundException when the transformable with a certain tag is not found.
$transformer->tryTransform($data);

// uses a default transformable (PlaceboTreeTransformer by default) when the transformable
// with a certain tag is not found.
$transformer->transformOrDefault($data);
```

#### Usage with method supplied transformables

```php
$transformer = new TreeTransformer;

$transformableMap = new TreeTransformableTagReadOnlyMap([
    $stringTreeTransformable,
    new CustomTreeTransformable
]);

try {
    $transformer->tryTransform($data, $transformableMap);
} catch(NotfoundException $e) {
    // thrown if transformable is not found
}

$transformer->transformOrDefault($data, $transformableMap);

//it is also possible to supply a new default transformable
$transformer->transformOrDefault($data, $transformableMap, $stringTreeTransformable);
```

#### Usage with constructor or default supplied transformables

```php
$transformableMap = new TreeTransformableTagReadOnlyMap([
    $stringTreeTransformable,
    new CustomTreeTransformable
]);

$transformer = new TreeTransformer($stringTreeTransformable, $transformableMap);

try {
    $transformer->tryTransform($data);
} catch(NotfoundException $e) {
    // thrown if transformable is not found
}

$transformer->transformOrDefault($data);
```