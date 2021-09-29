<?php

namespace Danepowell\dp6502;

use Exception;

/**
 * A data bus connecting MPU, ROM, and RAM.
 */
class DataBus {

  private ROM $rom;

  public function __construct(ROM $rom) {
    $this->rom = $rom;
  }

  public function read(int $address): Byte {
    // RAM
    if ($address >= 0 && $address <= 0x3fff) {
      throw new Exception('RAM not yet defined');
    }

    // VIA
    if ($address >= 0x6000 && $address <= 0x7fff) {
      throw new Exception('VIA not yet defined');
    }

    // ROM
    if ($address >= 0x8000 && $address <= 0xffff) {
      // Highest order bit is dropped when addressing the ROM.
      return $this->rom->read($address - 0x8000);
    }

    throw new Exception('Invalid address');

  }

}
