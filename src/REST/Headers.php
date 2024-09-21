<?php
namespace Krishna\Utilities\REST;

final class Headers implements \ArrayAccess, \IteratorAggregate, \Countable, \Stringable {
	private array $headers = [];
	
	public function __construct(?array $headers = null) {
		if($headers !== null) {
			foreach($headers as $key => $value) {
				$this->headers[(string)$key] = (string)$value;
			}
		}
	}

	// For ArrayAccess interface
	public function offsetExists(mixed $offset): bool {
		return isset($this->headers[(string)$offset]);
	}

	// For ArrayAccess interface
	public function offsetGet(mixed $offset): mixed {
		return $this->headers[(string)$offset] ?? null;
	}

	// For ArrayAccess interface
	public function offsetSet(mixed $offset, mixed $value): void {
		if($value === null) {
			unset($this->headers[(string)$offset]);
			return;
		}
		$this->headers[(string)$offset] = (string)$value;
	}

	// For ArrayAccess interface
	public function offsetUnset(mixed $offset): void {
		unset($this->headers[(string)$offset]);
	}

	// For IteratorAggregate interface
	public function getIterator(): \Traversable {
		return new \ArrayIterator($this->headers);
	}

	// For Countable interface
	public function count(): int {
		return count($this->headers);
	}

	// For Stringable interface
	public function __toString(): string {
		if(count($this->headers) === 0) {
			return '';
		}
		$result = [];
		foreach($this->headers as $key => $value) {
			$result[] = "{$key}: {$value}";
		}
		return implode("\r\n", $result);
	}

	public function toArray(): array {
		$ret = [];
		foreach($this->headers as $key => $value) {
			$ret[] = $key . ': ' . $value;
		}
		return $ret;
	}

	public function keys(): array {
		return array_keys($this->headers);
	}

	public function clear(): self {
		$this->headers = [];
		return $this;
	}

	public function merge(Headers|array $headers): self {
		if($headers instanceof Headers) {
			$headers = $headers->headers;
		}
		foreach($headers as $key => $value) {
			$this->headers[(string)$key] = (string)$value;
		}
		return $this;
	}
}