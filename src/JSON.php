<?php
namespace Krishna\Utilities;

final class JSON {
	use StaticOnlyTrait;
	public static function encode(mixed $object, bool $pretty = false) : string {
		$options = JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE;
		if($pretty) {
			$options |= JSON_PRETTY_PRINT;
		}
		$out = json_encode($object, $options);
		return ($out === false) ? 'null' : $out;
	}

	public static function decode(string $json) : mixed { // Returns null on error
		return json_decode($json, true, flags: JSON_INVALID_UTF8_SUBSTITUTE);
	}
}