<?php
namespace Krishna\Utilities\REST;

use Krishna\Utilities\JSON;

enum EnumEncoder: string {
	case JSON = 'application/json';
	case FormURLEncoded = 'application/x-www-form-urlencoded';
	case MultipartFormData = 'multipart/form-data';
}