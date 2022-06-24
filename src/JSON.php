<?php
namespace Krishna\Utilities;

class JSON {
	use StaticOnlyTrait;
	public static function encode(mixed $object, bool $pretty = false, bool $convert_html_special_chars = false) : string {
		$options = JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE;
		if($pretty) {
			$options |= JSON_PRETTY_PRINT;
		}
		$out = json_encode($object, $options);
		if($out === false) { return 'null'; }
		
		return $convert_html_special_chars ? htmlspecialchars(
			string: $out,
			encoding: 'UTF-8',
			flags: ENT_SUBSTITUTE | ENT_NOQUOTES | ENT_HTML5
		) : $out;
	}

	public static function decode(string $json) : mixed { // Returns null on error
		return json_decode($json, true, flags: JSON_INVALID_UTF8_SUBSTITUTE);
	}
}