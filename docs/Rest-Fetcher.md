# Rest-Fetcher

`Rest-Fetcher` is a PHP utility class for making HTTP requests with support for various HTTP methods, headers, and data encoding formats. It is part of the `Krishna\Utilities\REST` namespace.

## Features

- Supports `GET`, `POST`, `PUT`, and `DELETE` HTTP methods.
- Handles JSON, Form URL Encoded, and Multipart Form Data encoding.
- Manages custom headers and user agents.
- Supports gzip and deflate content encoding.
- Handles redirects with a configurable maximum number of redirects.

## Usage

### Initialization

Create an instance of the `Fetcher` class by providing the base URI and optional headers, user agent, and maximum redirects.

```php
use Krishna\Utilities\REST\Fetcher;
use Krishna\Utilities\REST\Headers;
use Krishna\Utilities\REST\EnumUserAgent;

$fetcher = new Fetcher(
	baseURI: 'https://api.example.com',
	headers: new Headers(['Authorization' => 'Bearer token']),
	userAgent: EnumUserAgent::Browser,
	maxRedirects: 5
);
```

### Making Requests

Use the `fetch` method to make HTTP requests. You can specify the path, data, method, data encoder, additional headers, and maximum redirects.

```php
$response = $fetcher->fetch(
	path: '/endpoint',
	data: ['key' => 'value'],
	method: EnumMethod::POST,
	dataEncoder: EnumEncoder::JSON,
	additionalHeaders: new Headers(['Custom-Header' => 'value']),
	maxRedirects: 3
);
```

### Handling Responses

The `fetch` method returns an instance of the `FetchResult` class, which contains details about the request and response, as well as any errors that occurred.

```php
echo ($response->error !== null) ? "Error: " . $response->error : "Success";

echo "Request URL: " . $response->request->url;
echo "Request Data: " . json_encode($response->request->data);

echo "Response Data: " . json_encode($response->response->data);
```

## License

This project is licensed under the MIT License. See the [LICENSE](../LICENSE) file for details.