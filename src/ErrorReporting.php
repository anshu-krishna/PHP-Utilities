<?php
namespace Krishna\Utilities;

class ErrorReporting {
	use StaticOnlyTrait;
	/* Configs */
	public static bool $dump_trace = true;
	public static bool $compact = true;

	private static $echo_callback = null;
	private static function default_echo_callback(mixed $value) {
		echo
			'<pre><strong style="color:red;font-size:1.2em;">Error:</strong> ',
			JSON::encode($value, true, true),
			'</pre>';
	}

	private static function compact_trace_item(array $item) {
		$item['type'] ??= '';
		$item['class'] ??= '';
		$item['function'] ??= '';
		$item['file'] ??= 'Unknown';
		$item['line'] ??= 'Unknown';
		$func = "{$item['class']}{$item['type']}{$item['function']}";
		$ret = [
			'at' => "File: {$item['file']}; Line: {$item['line']}"
		];
		if(strlen($func) > 0) { $ret['called'] = $func; }
		if(isset($item['object'])) { $ret['object'] = $item['object']; }
		return $ret;
	}

	private static function execute_callback(
		string $file, int $line, string $msg,
		?array $trace = null
	) {
		$data = null;
		if(static::$compact) {
			$data = [ 'at' => "File: {$file}; Line: {$line}" ];
		} else {
			$data = ['file' => $file, 'line' => $line ];
		}
		$data['msg'] = $msg;
		if($trace !== null) {
			$data['trace'] = $trace;
			if(static::$compact) {
				$data['trace'] = array_map([static::class, 'compact_trace_item'], $data['trace']);
			}
		}
		(static::$echo_callback)($data);
	}

	public static function init(?callable $echo_callback = null) {
		/* Allow exectution only once */
		if(static::$echo_callback !== null) { return; }

		/* Setup callback function */
		if(is_callable($echo_callback)) {
			static::$echo_callback = $echo_callback;
		} else {
			static::$echo_callback = [static::class, 'default_echo_callback'];
		}

		/* Disable default reporting */
		\error_reporting(0);

		/* Setup handlers */
		\register_shutdown_function(function () {
			$error = error_get_last();
			if($error === null) { return; }
			static::execute_callback(
				file: $error['file'], line: $error['line'], msg: $error['message'],
				trace: static::$dump_trace ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) : null
			);
		});
		\set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline) {
			static::execute_callback(
				file: $errfile, line: $errline, msg: $errstr,
				trace: static::$dump_trace ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) : null
			);
			error_clear_last();
		}, E_ALL | E_STRICT);
		\set_exception_handler(function(\Throwable $exception) {
			$class = get_class($exception);
			static::execute_callback(
				file: $exception->getFile(), line: $exception->getLine(), msg: $exception->getMessage(),
				trace: static::$dump_trace ? $exception->getTrace() : null
			);
		});
	}
}