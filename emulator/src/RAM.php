<?php

namespace Danepowell\dp6502;

/**
 * RAM.
 */
class RAM {

  private array $data;

  public function read(int $address): int {
    Util::validateAddress($address);
    return $this->data[$address];
  }

  public function write(int $address, int $data): void {
    Util::validateAddress($address);
    Util::validateByte($data);
    $this->data[$address] = $data;
  }

}
