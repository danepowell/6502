<?php

namespace Danepowell\dp6502;

/**
 * Emulate a 6502 MPU.
 */
class MPU {

  private DataBus $dataBus;

  // Program counter (PC) register.
  private int $regPC;

  // Instruction (opcode) register.
  private int $regOp;

  // Temporary address register for first operand.
  private int $regAd;

  // Registers A,X,Y.
  private int $regA;
  private int $regX;
  private int $regY;

  public function __construct(DataBus $dataBus) {
    $this->dataBus = $dataBus;
  }

  public function reset(): void {
    // Clear registers.
    $this->regOp = 0;
    $this->regAd = 0;
    $this->regPC = 0;
    $this->regA = $this->regX = $this->regY = 0;

    // Get the reset vector.
    $this->regPC = 0xfffc;
    $vectorLo = $this->read();
    $vectorHi = $this->read();
    $this->regPC = $vectorHi * 256 + $vectorLo;
  }

  public function tock(): void {
      if (!$this->regOp) {
        $this->regOp = $this->read();
        return;
      }

      switch ($this->regOp) {
        // lda
        case 0xa9:
          $this->regA = $this->read();
          $this->regOp = 0;
          return;
        // sta
        case 0x8d:
          if (!$this->regAd) {
            $this->regAd = $this->read();
            return;
          }
          $addressHi = $this->read();
          $this->write($addressHi * 256 + $this->regAd, $this->regA);
          $this->regOp = 0;
          $this->regAd = 0;
          return;
        // jsr
        case 0x20:
          $this->regPC = $this->read();
          $this->regOp = 0;
          break;
        default:
          throw new \Exception('Unknown OpCode ' . Util::byteHex($this->regOp));
      }
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
