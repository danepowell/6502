<?php

namespace Danepowell\dp6502;

use Exception;

/**
 * A data bus connecting MPU, ROM, and RAM.
 */
class DataBus {

  private array $chips;

  public function __construct(array $chips) {
    $this->chips = $chips;
  }

  public function read(int $address): int {
    Util::validateAddress($address);
    $chip = $this->selectChip($address);
    return $chip->read($address - $chip->addressStart());
  }

  public function write(int $address, int $data): void {
    Util::validateAddress($address);
    $chip = $this->selectChip($address);
    $chip->write($address - $chip->addressStart(), $data);
  }

  private function selectChip($address): Chip {
    foreach ($this->chips as $chip) {
      if ($address >= $chip->addressStart() && $address <= $chip->addressEnd()) {
        return $chip;
      }
    }
    throw new Exception('Address ' . Util::addressHex($address) . ' does not map to any device');
  }

}
