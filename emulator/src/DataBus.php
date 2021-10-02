<?php

namespace Danepowell\dp6502;

use Exception;

/**
 * A data bus connecting MPU, ROM, and RAM.
 */
class DataBus {

  private ROM $rom;
  private RAM $ram;

  public function __construct(ROM $rom, RAM $ram) {
    $this->rom = $rom;
    $this->ram = $ram;
  }

  public function read(int $address): int {
    Util::validateAddress($address);
    // RAM
    if ($address >= 0 && $address <= 0x3fff) {
      return $this->ram->read($address);
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

  public function write(int $address, int $data): void {
    Util::validateAddress($address);
    // RAM
    if ($address >= 0 && $address <= 0x3fff) {
      $this->ram->write($address, $data);
      return;
    }

    // VIA
    if ($address >= 0x6000 && $address <= 0x7fff) {
      throw new Exception('VIA not yet defined');
    }

    // ROM
    if ($address >= 0x8000 && $address <= 0xffff) {
      throw new Exception("It's called _read-only_ for a reason, dummy");
    }

    throw new Exception('Address ' . Util::addressHex($address) . ' does not map to any device');
  }

}
