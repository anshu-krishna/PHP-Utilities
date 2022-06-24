<?php
namespace Krishna\Utilities;

class Base64 {
	use StaticOnlyTrait;
	public static function encode(string $str) : string {
		return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
	}
	public static function decode(string $str, $non_binary = false) : ?string {
		$str = strtr($str, '-_', '+/');
		if(preg_match("/^[a-zA-Z0-9\/+]*={0,2}$/", $str)) {
			if($non_binary) {
				$b64 = base64_decode($str, true);
				return ($b64 === json_decode(json_encode($b64, JSON_PARTIAL_OUTPUT_ON_ERROR))) ? $b64 : null;
			} else {
				$ret = base64_decode($str, true);
				return ($ret === false) ? null : $ret;
			}
		}
		return null;
	}
	public static function encode_json($value) : string {
		return static::encode(JSON::encode($value));
	}
	public static function decode_json(string $str) { // Returns null on error
		if(($str = static::decode($str)) === null) {
			return null;
		}
		return JSON::decode($str);
	}
}