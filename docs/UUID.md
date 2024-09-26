# UUID Utility

This library provides a utility class for generating UUIDs in PHP.

This is a static-only class, meaning it cannot be instantiated. All methods are static and can be called directly on the class.

### Methods

#### `gen(bool $use_com = false): string`

Generates a UUID.

- **Parameters:**
	- `use_com` (bool): If set to `true`, the method will attempt to use the `com_create_guid` function to generate a UUID. If this fails, it will fall back to generating a UUID using random bytes.

- **Returns:**
	- `string`: The generated UUID.

- **Example:**
	```php
	use Krishna\Utilities\UUID;

	// Generate a UUID using random bytes
	$uuid = UUID::gen();

	// Generate a UUID using COM (if available)
	$uuid_com = UUID::gen(true);
	```

## License

This project is licensed under the MIT License.































































