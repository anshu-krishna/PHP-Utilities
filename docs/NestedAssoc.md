# NestedAssoc

`NestedAssoc` is a PHP class that provides a way to manage nested associative arrays with ease. It implements `ArrayAccess`, `IteratorAggregate`, `Countable`, and `JsonSerializable` interfaces.

## Features

- Access nested array elements using dot notation.
- Iterate over all keys in the nested array.
- Count all keys in the nested array.
- JSON serialization of the nested array.


## Usage

### Creating an Instance

```php
use Krishna\Utilities\NestedAssoc;

$data = [
	'user' => [
		'name' => 'John Doe',
		'email' => 'john@example.com'
	]
];

$nestedAssoc = new NestedAssoc($data);
```

### Accessing Elements

```php
echo $nestedAssoc['user.name']; // Outputs: John Doe
```

### Setting Elements

```php
$nestedAssoc['user.age'] = 30;

/*
The internal array now looks like this:
[
    'user' => [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'age' => 30
    ]
]
*/
```

### Unsetting Elements

```php
unset($nestedAssoc['user.email']);

/*
The internal array now looks like this:
[
	'user' => [
		'name' => 'John Doe',
		'age' => 30
	]
]
*/
```

### Iterating Over Elements

```php
foreach ($nestedAssoc as $key => $value) {
	echo "$key => $value\n";
}

/*
Outputs:
user.name => John Doe
user.email => john@example.com
user.age => 30
*/
```

### Counting Elements

#### Counting Top-Level Elements

```php
echo count($nestedAssoc); // Outputs the count of top-level elements
```

#### Counting All Keys

```php
echo $nestedAssoc->countItems(); // Outputs the count of all keys in the nested array
```

### JSON Serialization

```php
echo json_encode($nestedAssoc); // Outputs the JSON representation of the nested array
```

## License

This project is licensed under the MIT License.