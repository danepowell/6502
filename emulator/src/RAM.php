<?php

namespace Danepowell\dp6502;

/**
 * RAM.
 */
class RAM extends Chip {

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

  public function addressStart(): int {
    return 0;
  }

  public function addressEnd(): int {
    return 0x3fff;
  }

}
