<?php
namespace Krishna\Utilities;

class NestedAssoc implements \ArrayAccess, \IteratorAggregate, \Countable, \JsonSerializable {
	private static function iAllDotKeys(array $store, array $keys = []) : iterable {
		foreach ($store as $key => $value) {
			$keys[] = $key;
			if (is_array($value)) {
				yield from self::iAllDotKeys($value, $keys);
			} else {
				yield implode('.', $keys);
			}
			array_pop($keys);
		}
	}	
	
	protected array $data = [];

	public function __construct(array $data = []) {
		$this->data = $data;
	}

	// Functions for ArrayAccess
	public function offsetExists(mixed $offset): bool {
		$path = explode('.', (string) $offset);
		$store = $this->data;
		foreach ($path as $key) {
			if(is_array($store) && array_key_exists($key, $store)) {
				$store = $store[$key];
			} else {
				return false;
			}
		}
		return true;
	}
	public function offsetGet(mixed $offset): mixed {
		$path = explode('.', (string) $offset);
		$store = $this->data;
		foreach ($path as $key) {
			if(is_array($store) && array_key_exists($key, $store)) {
				$store = $store[$key];
			} else {
				return null;
			}
		}
		return $store;
	}
	public function offsetSet(mixed $offset, mixed $value): void {
		$path = explode('.', (string) $offset);
		$store = &$this->data;
		foreach ($path as $key) {
			if(is_array($store)) {
				if(!array_key_exists($key, $store)) {
					$store[$key] = [];
				}
				$store = &$store[$key];
			} else {
				throw new \InvalidArgumentException('Cannot set value to non-array key');
			}
		}
		$store = $value instanceof self ? $value->data : $value;
	}
	public function offsetUnset(mixed $offset): void {
		$path = explode('.', (string) $offset);
		$lastKey = array_pop($path);
		$store = &$this->data;
		foreach ($path as $key) {
			if(is_array($store) && array_key_exists($key, $store)) {
				$store = &$store[$key];
			} else {
				return;
			}
		}
		if(is_array($store)) {
			unset($store[$lastKey]);
		}
	}

	// Functions for IteratorAggregate
	public function getIterator(): \Traversable {
		return new \ArrayIterator($this->data);
	}

	// Functions for Countable
	public function count(): int {
		return count($this->data);
	}

	// Functions for JsonSerializable
	public function jsonSerialize(): mixed {
		return $this->data;
	}

	// Custom function to iterate over all keys
	public function items() : iterable {
		foreach(self::iAllDotKeys($this->data) as $key) {
			yield $key => $this[$key];
		}
	}

	// Custom function to count all keys
	public function countItems() : int {
		$count = 0;
		foreach(self::iAllDotKeys($this->data) as $key) {
			$count++;
		}
		return $count;
	}

	public function getAssoc() : array {
		return $this->data;
	}
	
	public function getDotAssoc() : array {
		$dotAssoc = [];
		foreach(self::iAllDotKeys($this->data) as $key) {
			$dotAssoc[$key] = $this[$key];
		}
		return $dotAssoc;
	}
}