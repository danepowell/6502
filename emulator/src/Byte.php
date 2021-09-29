<?php

namespace Danepowell\dp6502;

/**
 * Byte data type.
 */
class Byte {
  private int $byte;

  public function __construct(int $byte) {
    $this->byte = $byte;
  }

  public function hex(): string {
    return str_pad(base_convert($this->byte, 10, 16), 2, '0');
  }

  public function bin(): string {
    return str_pad(base_convert($this->byte, 10, 2), 8, '0');
  }

}
