<?php

namespace Danepowell\dp6502;

/**
 * Bits data type.
 */
abstract class Bits {
  private int $bits;

  public function __construct(int $bits) {
    if ($bits > 2**$this->length()) {
      throw new \Exception("Data $bits exceeds allowed size of " . $this->length() . " bits");
    }
    $this->bits = $bits;
  }

  public function __toString(): string {
    return $this->hex();
  }

  abstract protected function length(): int;

  public function hex(): string {
    return '0x' . str_pad(dechex($this->bits), $this->length()/4, '0');
  }

  public function bin(): string {
    return '0b' . str_pad(decbin($this->bits), $this->length(), '0');
  }

  public function int(): int {
    return $this->bits;
  }

  public function increment(): void {
    $this->bits++;
  }

}
