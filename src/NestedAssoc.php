<?php
namespace Krishna\Utilities;

class NestedAssoc implements \ArrayAccess, \IteratorAggregate, \Countable, \JsonSerializable {
	private static function keysList(array $store, array $keys = []) : iterable {
		foreach ($store as $key => $value) {
			$keys[] = $key;
			if (is_array($value)) {
				yield from self::keysList($value, $keys);
			} else {
				yield implode('.', $keys);
			}
			array_pop($keys);
		}
	}
	
	private array $data = [];

	public function __construct(array $data = []) {
		$this->data = $data;
	}

	private function keyChain(
		mixed $path,
		array &$deepArray,
		bool $create = false
	) : bool {
		$keys = explode('.', (string) $path);
		$lastKey = array_pop($keys);
		
		foreach ($keys as $key) {
			if (!array_key_exists($key, $deepArray)) {
				if ($create) {
					$deepArray[$key] = [];
				} else {
					return false;
				}
			}
			$deepArray = &$deepArray[$key];
		}

		return $lastKey;
	}

	// Functions for ArrayAccess
	public function offsetExists(mixed $offset): bool {
		$deepArray = &$this->data;
		$lastKey = $this->keyChain($offset, $deepArray);
		return $lastKey !== false && array_key_exists($lastKey, $deepArray);
	}
	public function offsetGet(mixed $offset): mixed {
		$deepArray = &$this->data;
		$lastKey = $this->keyChain($offset, $deepArray);
		if($lastKey === false) {
			return null;
		}
		if(array_key_exists($lastKey, $deepArray)) {
			return $deepArray[$lastKey];
		}
		return null;
	}
	public function offsetSet(mixed $offset, mixed $value): void {
		$deepArray = &$this->data;
		$lastKey = $this->keyChain($offset, $deepArray, true);
		$deepArray[$lastKey] = $value instanceof self ? $value->data : $value;
	}
	public function offsetUnset(mixed $offset): void {
		$deepArray = &$this->data;
		$lastKey = $this->keyChain($offset, $deepArray);
		if ($lastKey !== false) {
			unset($deepArray[$lastKey]);
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
		foreach(self::keysList($this->data) as $key) {
			yield $key => $this[$key];
		}
	}

	// Custom function to count all keys
	public function countItems() : int {
		$count = 0;
		foreach(self::keysList($this->data) as $key) {
			$count++;
		}
		return $count;
	}
}