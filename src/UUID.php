<?php
namespace Krishna\Utilities;

class UUID {
	use StaticOnlyTrait;

	public static function gen(bool $use_com = false) {
		if($use_com) {
			try {
				return trim(com_create_guid(), "{}");
			} catch (\Throwable $th) {}
		}
		$data = random_bytes(16);
		$data[6] = chr(ord($data[6]) & 0x0f | 0x40);
		$data[8] = chr(ord($data[8]) & 0x3f | 0x80);
		return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));	
	}
}