<?php

namespace Danepowell\dp6502\Chips;

use Danepowell\dp6502\Util;

/**
 * ROMChip.
 */
class ROMChip extends AbstractChip {

  private array $program;

  public function __construct(string $rom_file) {
    $this->program = array_values(unpack('C*', file_get_contents($rom_file)));
  }

  public function read($address): int {
    Util::validateAddress($address);
    return $this->program[$address];
  }

  public function write(int $address, int $data): void {
    throw new \Exception("It's called _read-only_ for a reason, dummy");
  }

  public function addressStart(): int {
    return 0x8000;
  }

  public function addressEnd(): int {
    return 0xffff;
  }
}
