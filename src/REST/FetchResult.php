<?php
namespace Krishna\Utilities\REST;

use stdClass;

readonly class FetchResult {
	public ?string $error;
	public stdClass $response;
	public stdClass $request;

	public function __construct(
		string $url,
		?array $request,
		?array $requestHeaders,

		mixed $response,
		?array $responseHeaders,
		
		?string $error,
	) {
		$this->error = $error;
		$this->request = (object) [
			'url' => $url,
			'data' => $request,
			'headers' => $requestHeaders,
		];
		$this->response = (object) [
			'data' => $response,
			'headers' => $responseHeaders,
		];
	}
}