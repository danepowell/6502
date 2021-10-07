<?php

namespace Danepowell\dp6502\Chips;

use Danepowell\dp6502\DataBus;
use Danepowell\dp6502\Util;

/**
 * Emulate a 6502 MPUChip.
 */
class MPUChip {

  private DataBus $dataBus;
  private static array $opMatrix = [
    0xa9 => 'lda',
    0x8d => 'sta',
    0x6a => 'ror',
    0x4c => 'jmp',
  ];

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
    $this->jmp();
    $this->loop();
  }

  /**
   * Main program loop.
   *
   * @uses lda()
   * @uses sta()
   * @uses ror()
   * @uses jmp()
   * @throws \Exception
   */
  public function loop(): void {
    do {
      $opCode = $this->readByte();
      if (!array_key_exists($opCode, self::$opMatrix)) {
        throw new \Exception('Unknown OpCode ' . Util::byteHex($opCode));
      }
      $function = self::$opMatrix[$opCode];
      $this->$function();
    }
    while (TRUE);
  }

  private function lda(): void {
    $this->regA = $this->readByte();
  }

  private function sta(): void {
    $this->write($this->readAddress(), $this->regA);
  }

  private function ror(): void {
    $binary = Util::decBin($this->regA, 8);
    $this->regA = bindec(substr($binary, -1).substr($binary, 0, -1));
  }

  private function jmp(): void {
    $this->regPC = $this->readAddress();
  }

  private function readByte(): int {
    $data = $this->dataBus->read($this->regPC);
    if (getenv('DP6502_DEBUG')) {
      echo 'Read ' . Util::addressHex($this->regPC) . ': ' . Util::byteHex($data) . "\n";
    }
    $this->regPC++;
    return $data;
  }

  private function readAddress(): int {
    $addressLo = $this->readByte();
    $addressHi = $this->readByte();
    return $addressHi * 256 + $addressLo;
  }

  private function write(int $address, int $data): void {
    if (getenv('DP6502_DEBUG')) {
      echo 'Write ' . Util::addressHex($address) . ': ' . Util::byteHex($data) . "\n";
    }
    try {
      $this->dataBus->write($address, $data);
    }
    catch (\Exception $exception) {
      echo 'Could not write: ' . $exception->getMessage() . "\n";
      exit;
    }
  }

}
