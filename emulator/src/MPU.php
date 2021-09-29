<?php

namespace Danepowell\dp6502;

/**
 * Emulate a 6502 MPU.
 */
class MPU {

  private DataBus $dataBus;

  public function __construct(DataBus $dataBus) {
    $this->dataBus = $dataBus;
  }

  public function reset(): void {
    $addrLo = $this->dataBus->read(0xfffc);
    $addrHi = $this->dataBus->read(0xfffd);
    echo $addrHi->hex() . $addrLo->hex();
  }

}
