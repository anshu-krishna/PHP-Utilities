# StaticOnlyTrait

The `StaticOnlyTrait` is a PHP trait designed to enforce static-only usage of a class. It prevents instantiation and provides a method to retrieve static properties of the class.

## Usage

To use the `StaticOnlyTrait`, include it in your class using the `use` keyword.

```php
class MyClass {
	use \Krishna\Utilities\StaticOnlyTrait;

	public static $example = 'example';
}

// Retrieve static properties
$staticProperties = MyClass::__getStaticProperties__();
print_r($staticProperties);
```

## Methods

### `__construct()`

This constructor is protected and final, preventing the instantiation of the class.

### `__getStaticProperties__()`

This static method returns an array of static properties of the class.

```php
public static function __getStaticProperties__() : array
```

## Example

```php
class ExampleClass {
	use \Krishna\Utilities\StaticOnlyTrait;

	public static $foo = 'bar';
	public static $baz = 'qux';
}

$staticProps = ExampleClass::__getStaticProperties__();
print_r($staticProps);
// Output:
// Array
// (
//     [foo] => bar
//     [baz] => qux
// )
```

## License

This project is licensed under the MIT License.






























































