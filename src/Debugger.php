<?php
namespace Krishna\Utilities;

class Debugger {
	use StaticOnlyTrait;

	public static $dumpper_callback = null;

	private static function get_function_name($trace) : string {
		$type = $trace['type'] ?? '';
		$class = $trace['class'] ?? '';
		$name = $trace['function'] ?? '';
		return "{$class}{$type}{$name}";
	}

	public static function trace_call_point(?string $func_name = null, bool $compact = true) : array {
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		if($func_name === null) {
			$trace = $trace[2] ?? $trace[1] ?? $trace[0];
		} else {
			$found = false;
			$trace_count = count($trace) - 1;
			for($i = $trace_count; $i >= 0; $i--) {
				$name = static::get_function_name($trace[$i]);
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
		
		$trace['file'] ??= 'Unknown';
		$trace['line'] ??= 'Unknown';
		
		$location = [];
		if($compact) {
			$location['at'] = "File: {$trace['file']}; Line: {$trace['line']}";
		} else {
			$location['file'] = $trace['file'];
			$location['line'] = $trace['line'];
		}
		$location['called'] = static::get_function_name($trace);
		return $location;
	}

	public static function dump(mixed $value, ?string $title = null, ?string $callpoint = null, bool $compact = true) : array {
		$trace = static::trace_call_point($callpoint ?? (__METHOD__), $compact);
		unset($trace['called']);
		if($title !== null) { $trace['title'] = $title; }
		$trace['value'] = $value;
		if(is_callable(static::$dumpper_callback)) {
			(static::$dumpper_callback)($trace);
		}
		return $trace;
	}
}