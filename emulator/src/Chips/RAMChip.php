<?php

namespace Danepowell\dp6502\Chips;

use Danepowell\dp6502\Util;

/**
 * RAMChip.
 */
class RAMChip extends AbstractChip {

  private array $data;

  public function __construct() {
    $this->data = array_fill(0, $this->addressEnd() - $this->addressStart(), 0);
  }

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
