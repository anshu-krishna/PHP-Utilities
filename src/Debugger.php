<?php
namespace Krishna\Utilities;

final class Debugger {
	use StaticOnlyTrait;

	public static $dumpper = null;

	private static function get_function_name($trace) : string {
		$name = $trace['class'] ?? null;
		if($name === null) {
			return $trace['function'];
		} else {
			return $name . '::' . $trace['function'];
		}
	}

	public static function trace_call_point(?string $func_name = null) : array {
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		if($func_name === null) {
			$trace = $trace[2] ?? $trace[1] ?? $trace[0];
		} else {
			$found = false;
			$trace_count = count($trace) - 1;
			for($i = $trace_count; $i >= 0; $i--) {
				$name = self::get_function_name($trace[$i]);
				if($name === $func_name) {
					$found = true;
					break;
				}
			}
			if($found) {
				$trace = $trace[$i];
			} else {
				$trace = $trace[$trace_count];
			}
		}
		$location = [
			'line' => $trace['line'] ?? 'Unknown',
			'file' => $trace['file'] ?? 'Unknown',
			'call_to' => self::get_function_name($trace)
		];
		return $location;
	}

	public static function dump(string $key, mixed $value, ?string $callpoint = null) {
		$trace = self::trace_call_point($callpoint ?? (__METHOD__));
		unset($trace['call_to']);
		$trace['msg'] = "Debug dump: [{$key}]";
		$trace['value'] = $value;
		if(is_callable(self::$dumpper)) {
			(self::$dumpper)($trace);
		} else {
			return $trace;
		}
	}
}