<?php

namespace Danepowell\dp6502;

/**
 * Emulate a 6502 MPU.
 */
class MPU {

  private DataBus $dataBus;

  // Program counter (PC) register.
  private Address $regPC;

  // Registers A,X,Y.
  private Byte $regA;
  private Byte $regX;
  private Byte $regY;

  public function __construct(DataBus $dataBus) {
    $this->dataBus = $dataBus;
  }

  public function reset(): void {
    // Get the reset vector.
    $vectorLo = $this->dataBus->read(new Address(0xfffc));
    $vectorHi = $this->dataBus->read(new Address(0xfffd));
    $this->regPC = new Address($vectorHi->int() * 256 + $vectorLo->int());
    $this->loop();
  }

  public function loop(): void {
    do {
      $opCode = $this->dataBus->read($this->regPC);
      switch ($opCode->int()) {
        case 0xa9:
          // @todo add debug logging via env var that prints just like serial monitor.
          $this->regPC->increment();
          $this->regA = $this->dataBus->read($this->regPC);
          break;
        case 0x8d:
          $this->regPC->increment();
          // @todo write to data bus.
          break;
        default:
          throw new \Exception("Unknown OpCode $opCode");
      }
      $this->regPC->increment();
    }
    while (TRUE);
  }

}
