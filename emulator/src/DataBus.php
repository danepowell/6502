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

  public function read(Address $address): Byte {
    $addressInt = $address->int();
    // RAM
    if ($addressInt >= 0 && $addressInt <= 0x3fff) {
      throw new Exception("RAM not yet defined (address $addressInt)");
    }

    // VIA
    if ($addressInt >= 0x6000 && $addressInt <= 0x7fff) {
      throw new Exception('VIA not yet defined');
    }

    // ROM
    if ($addressInt >= 0x8000 && $addressInt <= 0xffff) {
      // Highest order bit is dropped when addressing the ROM.
      return $this->rom->read(new Address($addressInt - 0x8000));
    }

    throw new Exception('Invalid address');
  }

}
