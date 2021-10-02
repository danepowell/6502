<?php

namespace Danepowell\dp6502;

/**
 * Emulate a 6502 MPU.
 */
class MPU {

  private DataBus $dataBus;

  // Program counter (PC) register.
  private int $regPC;

  // Registers A,X,Y.
  private int $regA;
  private int $regX;
  private int $regY;

  public function __construct(DataBus $dataBus) {
    $this->dataBus = $dataBus;
  }

  public function reset(): void {
    // Get the reset vector.
    $this->regPC = 0xfffc;
    $vectorLo = $this->read();
    $vectorHi = $this->read();
    $this->regPC = $vectorHi * 256 + $vectorLo;
    $this->loop();
  }

  public function loop(): void {
    do {
      $opCode = $this->read();
      switch ($opCode) {
        case 0xa9:
          $this->regA = $this->read();
          break;
        case 0x8d:
          $address = $this->read();
          $this->write($address, $this->regA);
          break;
        case 0x20:
          $this->regPC = $this->read();
          break;
        default:
          throw new \Exception('Unknown OpCode ' . Util::byteHex($opCode));
      }
    }
    while (TRUE);
  }

  private function read(): int {
    $data = $this->dataBus->read($this->regPC);
    echo 'Read ' . Util::byteHex($data) . ' from ' . Util::addressHex($this->regPC) . "\n";
    $this->regPC++;
    return $data;
  }

  private function write(int $address, int $data): void {
    $this->dataBus->write($address, $data);
    echo 'Wrote ' . Util::byteHex($data) . ' to ' . Util::addressHex($address) . "\n";
  }

}
