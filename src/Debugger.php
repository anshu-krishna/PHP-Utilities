<?php
namespace Krishna\Utilities;

class Debugger {
	use StaticOnlyTrait;

	public static $dumpper_callback = null;
	public static bool $use_default_dumpper_callback = false;

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
			$max_i = count($trace) - 1;
			for($i = $max_i; $i > -1; $i--) {
				$name = static::get_function_name($trace[$i]);
				if($func_name === str_replace('->', '::', $name)) {
					$found = true;
					break;
				}
			}
			if($found) {
				$trace = $trace[$i];
			} else {
				$trace = $trace[$max_i];
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
		/*
			Suppose if function test() contains a call to the dump() function.

			Then pass (callpoint: __METHOD__) when calling dump to change
			the 'at' value in the dump to the position where the test() function
			was called.
		*/
		$trace = static::trace_call_point($callpoint ?? (__METHOD__), $compact);
		
		if($callpoint === null) { unset($trace['called']); }
		
		if($title !== null) { $trace['title'] = $title; }
		
		$trace['value'] = $value;
		
		if(is_callable(static::$dumpper_callback)) {
			(static::$dumpper_callback)($trace);
		} elseif(static::$use_default_dumpper_callback) {
			static::echo_print_r($trace);
		}
		
		return $trace;
	}

	public static function echo(array $data) {
		echo
			'<pre><strong style="color:blue;font-size:1.2em;">Debug:</strong> ',
			JSON::encode($data, true, true),
			'</pre>';
	}

	public static function echo_print_r(array $data) {
		echo '<pre><strong style="color:blue;font-size:1.2em;">Debug:</strong> ';
		print_r($data);
		echo '</pre>';
	}
}