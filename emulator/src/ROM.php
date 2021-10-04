<?php

namespace Danepowell\dp6502;

/**
 * ROM.
 */
class ROM extends Chip {

  private array $program;

  public function __construct(string $rom_file) {
    $this->program = array_values(unpack('C*', file_get_contents($rom_file)));
  }

  public function read($address): int {
    Util::validateAddress($address);
    return $this->program[$address];
  }

  public function write(int $address, $data): void {
    throw new \Exception("It's called _read-only_ for a reason, dummy");
  }

  public function addressStart(): int {
    return 0x8000;
  }

  public function addressEnd(): int {
    return 0xffff;
  }
}
