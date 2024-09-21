<?php
namespace Krishna\Utilities\REST;

use Krishna\Utilities\JSON;

class Fetcher {
	public readonly string $baseURI;
	public readonly Headers $headers;
	public readonly string $scheme;
	public readonly int $maxRedirects;

	public function __construct(
		string $baseURI,
		Headers|array|null $headers = null,
		EnumUserAgent|string|null $userAgent = EnumUserAgent::Krishna,
		int $maxRedirects = 5,
	) {
		// Ensure that the baseURI is a valid URI
		if(!filter_var($baseURI, FILTER_VALIDATE_URL)) {
			throw new \InvalidArgumentException('Invalid baseURI');
		}

		$baseURI = parse_url($baseURI);

		// Reform the baseURI except for the query and fragment
		$baseURI['scheme'] ??= 'https';
		if(!match($baseURI['scheme']) {
			'http' => true,
			'https' => true,
			default => false
		}) {
			throw new \InvalidArgumentException('Invalid baseURI; Scheme must be either http or https');
		}
		$this->scheme = $baseURI['scheme'];

		$uriParts = [
			$baseURI['scheme'],
			'://',
			$baseURI['host'],
			$baseURI['path'] ?? ''
		];

		// Trim the trailing slash
		$this->baseURI = rtrim(implode('', $uriParts), '/');

		if($headers === null) {
			$this->headers = new Headers();
		} elseif(is_array($headers)) {
			$this->headers = new Headers($headers);
		} else {
			$this->headers = $headers;
		}

		$this->maxRedirects = $maxRedirects;

		// Set the default headers
		if($userAgent !== null) {
			if(is_string($userAgent)) {
				$this->headers['User-Agent'] = $userAgent;
			} else {
				$this->headers['User-Agent'] = $userAgent->value;
			}
		}

		$this->headers['Accept-Encoding'] = 'gzip, deflate, identity';
		$this->headers['Connection'] = 'close';
	}

	protected static function normalize(array &$data) {
		foreach ($data as &$item) {
			if(is_array($item)) {
				static::normalize($item);
			} elseif(is_bool($item)) {
				$item = $item ? 'true' : 'false';
			} elseif ($item === null) {
				$item = '';
			}
		}
	}

	protected function buildMultipartFormData(array $data) : array {
		$eol = "\r\n";
		$testString = '';
		array_walk_recursive($data, function($value, $key) use (&$testString, $eol) {
			$testString .= $key . $eol . $value . $eol;
		});
		
		$size = 3;
		$boundary = '--' . bin2hex(random_bytes($size));
		
		// Ensure that the boundary is not in the test string
		while(strpos($testString, $boundary) !== false) {
			$boundary = '--' . bin2hex(random_bytes(++$size));	
		}

		$content = '--' . $boundary;
		$func = function($dataPart, $baseKey = null) use (&$content, $eol, $boundary, &$func) {
			foreach($dataPart as $key => $value) {
				$key = $baseKey !== null ? $baseKey . '[' . $key . ']' : $key;
				if(is_array($value)) {
					$func($value, $key);
				} else {
					$content .= $eol . 'Content-Disposition: form-data; name="' . $key . '"' . $eol . $eol . $value . $eol . '--' . $boundary;
				}
			}
		};
		$func($data);

		// Add the final boundary
		$content .= '--' . $eol;
		
		return [
			'boundary' => $boundary,
			'content' => $content
		];
	}

	public function fetch(
		string $path = '',
		?array $data = null,
		EnumMethod|string $method = EnumMethod::POST,
		EnumEncoder $dataEncoder = EnumEncoder::JSON,
		?Headers $additionalHeaders = null,
		?int $maxRedirects = null
	) {
		if(is_string($method)) {
			$method = strtoupper($method);
			$method = EnumMethod::tryFrom($method);
			if($method === null) {
				throw new \InvalidArgumentException('Invalid method. Expected one of ' . implode(', ', EnumMethod::cases()));
			}
		}

		$maxRedirects ??= $this->maxRedirects;

		$uri = $this->baseURI . $path;
		$context = [
			'method' => $method->value,
		];
		if($maxRedirects > 0) {
			$context['follow_location'] = 1;
			$context['max_redirects'] = $maxRedirects;
		}

		$headers = $this->headers;
		if($additionalHeaders !== null) {
			$headers = $headers->merge($additionalHeaders);
		}

		if($data !== null) {
			switch($method) {
				case EnumMethod::GET:
				case EnumMethod::DELETE:
					static::normalize($data);
					$uri .= '?' . http_build_query($data);
					break;
				case EnumMethod::POST:
				case EnumMethod::PUT:
					switch($dataEncoder) {
						case EnumEncoder::JSON:
							$context['content'] = JSON::encode($data);
							$headers['Content-Type'] = $dataEncoder->value . '; charset=utf-8';
							break;
						case EnumEncoder::FormURLEncoded:
							static::normalize($data);
							$context['content'] = http_build_query($data);
							$headers['Content-Type'] = $dataEncoder->value . '; charset=utf-8';
							break;
						case EnumEncoder::MultipartFormData:
							static::normalize($data);
							['boundary' => $boundary, 'content' => $content] = $this->buildMultipartFormData($data);
							$context['content'] = $content;
							$headers['Content-Type'] = $dataEncoder->value . '; boundary=' . $boundary;
							break;
					}
					break;
			}
			$context['header'] = $headers->__toString();
		}

		$error = null;
		$old_handler = set_error_handler(function(int $errno, string $errstr, string $errfile, int $errline) use (&$error) {
			$error = $errstr;
		});
		$http_response_header = [];
		$response = file_get_contents(
			filename: $uri,
			context: stream_context_create([$this->scheme => $context])
		);
		set_error_handler($old_handler);
		if($response === false) {
			$response = null;
		} else {
            // Check for Content-Encoding header and decompress if necessary
            foreach ($http_response_header as $header) {
                if (stripos($header, 'Content-Encoding: gzip') !== false) {
                    $response = gzdecode($response);
                    break;
                } elseif (stripos($header, 'Content-Encoding: deflate') !== false) {
                    $response = gzinflate($response);
                    break;
                }
            }
        }
		
		// return new FetchResult(
		// 	response: $response,
		// 	uri: $uri,
		// 	data: $data,
		// 	headers: [
		// 		'req' => $headers->toArray(),
		// 		'res' => $http_response_header
		// 	],
		// 	error: $error,
		// );

		return new FetchResult(
			url: $uri,
			request: $data,
			requestHeaders: $headers->toArray(),
			response: $response,
			responseHeaders: $http_response_header,
			error: $error,
		);
	}
}