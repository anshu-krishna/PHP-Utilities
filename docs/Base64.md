# Base64 Utility

This library provides utility functions for encoding and decoding Base64 strings, with support for JSON encoding and decoding.

This is a static-only class, meaning it cannot be instantiated. All methods are static and can be called directly on the class.


## Methods

### `Base64::encode`

Encodes a given string to a URL-safe Base64 string.

**Parameters:**
- `string $str` - The input string to encode.

**Returns:**
- `string` - The URL-safe Base64 encoded string.

**Example:**
```php
$encoded = Base64::encode('Hello, World!');
// $encoded will be 'SGVsbG8sIFdvcmxkIQ'
```

### `Base64::decode`

Decodes a given URL-safe Base64 string.

**Parameters:**
- `string $str` - The URL-safe Base64 encoded string to decode.
- `bool $non_binary` - Optional. If true, ensures the decoded string is non-binary. Default is false.

**Returns:**
- `string|null` - The decoded string, or null if decoding fails.

**Example:**
```php
$decoded = Base64::decode('SGVsbG8sIFdvcmxkIQ');
// $decoded will be 'Hello, World!'
```

### `Base64::encode_json`

Encodes a given value to a JSON string and then to a URL-safe Base64 string.

**Parameters:**
- `mixed $value` - The value to encode.

**Returns:**
- `string` - The URL-safe Base64 encoded JSON string.

**Example:**
```php
$encodedJson = Base64::encode_json(['key' => 'value']);
// $encodedJson will be a URL-safe Base64 encoded JSON string
```

### `Base64::decode_json`

Decodes a given URL-safe Base64 string to a JSON string and then decodes it to a PHP value.

**Parameters:**
- `string $str` - The URL-safe Base64 encoded JSON string to decode.

**Returns:**
- `mixed|null` - The decoded PHP value, or null if decoding fails.

**Example:**
```php
$decodedJson = Base64::decode_json($encodedJson);
// $decodedJson will be ['key' => 'value']
```

## License

This project is licensed under the MIT License.