<?php

namespace Danepowell\dp6502;

/**
 * Emulate a 6502 MPU.
 */
class MPU {

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
    $vectorLo = $this->read();
    $vectorHi = $this->read();
    $this->regPC = $vectorHi * 256 + $vectorLo;
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
      $opCode = $this->read();
      if (!array_key_exists($opCode, self::$opMatrix)) {
        throw new \Exception('Unknown OpCode ' . Util::byteHex($opCode));
      }
      $function = self::$opMatrix[$opCode];
      $this->$function();
    }
    while (TRUE);
  }

  private function lda(): void {
    $this->regA = $this->read();
  }

  private function sta(): void {
    $addressLo = $this->read();
    $addressHi = $this->read();
    $this->write($addressHi * 256 + $addressLo, $this->regA);
  }

  private function ror(): void {
    $binary = decbin($this->regA);
    $this->regA = bindec(substr($binary, -1).substr($binary, 0, -1));
  }

  private function jmp(): void {
    $this->regPC = $this->read();
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
