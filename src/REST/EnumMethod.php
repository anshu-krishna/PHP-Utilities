<?php
namespace Krishna\Utilities\REST;

enum EnumMethod: string {
	case GET = 'GET';
	case POST = 'POST';
	case PUT = 'PUT';
	case DELETE = 'DELETE';

	public function isSafe(): bool {
		return match($this->value) {
			'GET' => true,
			'HEAD' => true,
			'OPTIONS' => true,
			default => false
		};
	}
}