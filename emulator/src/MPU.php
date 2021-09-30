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
    $vectorLo = $this->dataBus->read(0xfffc);
    $vectorHi = $this->dataBus->read(0xfffd);
    $this->regPC = $vectorHi * 256 + $vectorLo;
    $this->loop();
  }

  public function loop(): void {
    do {
      $opCode = $this->dataBus->read($this->regPC);
      switch ($opCode) {
        case 0xa9:
          // @todo add debug logging via env var that prints just like serial monitor.
          $this->regPC++;
          $this->regA = $this->dataBus->read($this->regPC);
          break;
        case 0x8d:
          $this->regPC++;
          // @todo write to data bus.
          break;
        default:
          throw new \Exception("Unknown OpCode $opCode");
      }
      $this->regPC++;
    }
    while (TRUE);
  }

}
