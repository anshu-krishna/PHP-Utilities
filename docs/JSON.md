# JSON Utility

This document provides an overview of the `JSON` class in the `Krishna\Utilities` namespace.

This library provides utility functions for encoding and decoding JSON strings.

This is a static-only class, meaning it cannot be instantiated. All methods are static and can be called directly on the class.

### Methods

#### `encode`
Encodes a PHP variable into a JSON string.

**Signature:**
```php
public static function encode(mixed $object, bool $pretty = false, bool $convert_html_special_chars = false) : string
```

**Parameters:**
- `mixed $object`: The data to be encoded.
- `bool $pretty` (optional): If set to `true`, the JSON string will be pretty-printed. Default is `false`.
- `bool $convert_html_special_chars` (optional): If set to `true`, HTML special characters in the JSON string will be converted. Default is `false`.

**Returns:**
- `string`: The JSON-encoded string. Returns `'null'` if encoding fails.

**Example:**
```php
$data = ['name' => 'John', 'age' => 30];
$json = JSON::encode($data, true, true);
echo $json;
```

#### `decode`
Decodes a JSON string into a PHP variable.

**Signature:**
```php
public static function decode(string $json) : mixed
```

**Parameters:**
- `string $json`: The JSON string to be decoded.

**Returns:**
- `mixed`: The decoded data. Returns `null` if decoding fails.

**Example:**
```php
$json = '{"name":"John","age":30}';
$data = JSON::decode($json);
print_r($data);
```

## Notes
- The `encode` method uses `JSON_PARTIAL_OUTPUT_ON_ERROR` and `JSON_INVALID_UTF8_SUBSTITUTE` options by default.
- The `decode` method uses the `JSON_INVALID_UTF8_SUBSTITUTE` option.

## License

This project is licensed under the MIT License.