<?php
namespace Krishna\Utilities;

trait StaticOnlyTrait {
	final protected function __construct() {}
	final public static function __getStaticProperties__() {
		$class = new \ReflectionClass(static::class);
		return $class->getStaticProperties();
	}
}