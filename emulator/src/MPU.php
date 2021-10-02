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
        // lda
        case 0xa9:
          $this->regA = $this->read();
          break;
        // sta
        case 0x8d:
          $addressLo = $this->read();
          $addressHi = $this->read();
          $this->write($addressHi * 256 + $addressLo, $this->regA);
          break;
        // jsr
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
    echo 'Read ' . Util::addressHex($this->regPC) . ': ';
    $data = $this->dataBus->read($this->regPC);
    echo Util::byteHex($data) . "\n";
    $this->regPC++;
    return $data;
  }

  private function write(int $address, int $data): void {
    echo 'Write ' . Util::addressHex($address) . ': ' . Util::byteHex($data) . "\n";
    try {
      $this->dataBus->write($address, $data);
    }
    catch (\Exception $exception) {
      echo 'Could not write: ' . $exception->getMessage() . "\n";
      exit;
    }
  }

}
