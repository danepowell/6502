<?php

namespace Danepowell\dp6502;

/**
 * ROM.
 */
class ROM {

  private array $program;

  public function __construct(string $rom_file) {
    $this->program = array_values(unpack('C*', file_get_contents($rom_file)));
  }

  public function read($address): int {
    Util::validateAddress($address);
    return $this->program[$address];
  }

}
