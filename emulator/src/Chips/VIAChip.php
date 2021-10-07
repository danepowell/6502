<?php


namespace Danepowell\dp6502\Chips;

use Danepowell\dp6502\LEDs;
use Danepowell\dp6502\Util;

class VIAChip extends AbstractChip {
  private array $data;
  private int $ddrb;
  private int $orb;
  private LEDs $portb;

  public function __construct($portb) {
    $this->portb = $portb;
  }

  public function write(int $address, int $data): void {
    Util::validateAddress($address);
    Util::validateByte($data);
    switch ($address) {
      case 0:
        $this->orb = $data;
        $this->portb->write($data);
        break;
      case 2:
        $this->ddrb = $data;
        break;
      default:
        throw new \Exception('Unknown register number');
    }
    $this->data[$address] = $data;
  }

  public function read(int $address): int {
    throw new \Exception('Read not implemented');
  }

  public function addressStart(): int {
    return 0x6000;
  }

  public function addressEnd(): int {
    return 0x7fff;
  }
}