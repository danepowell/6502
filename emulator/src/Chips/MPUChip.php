<?php

namespace Danepowell\dp6502\Chips;

use Danepowell\dp6502\DataBus;
use Danepowell\dp6502\Util;
use Exception;

/**
 * Emulate a 6502 MPUChip.
 */
class MPUChip {

  private DataBus $dataBus;
  private int $cycles;
  private static array $opMatrix = [
    0xa9 => ['lda', '#'],
    0xad => ['lda', 'a'],
    0x8d => ['sta', 'a'],
    0x6a => ['ror', 'A'],
    0x4c => ['jmp', 'a'],
    0xa2 => ['ldx', '#'],
    0x9a => ['txs', 'i'],
    0x20 => ['jsr', 'a'],
    0x48 => ['pha', 's'],
  ];

  // Program counter (PC) register.
  private int $regPC;

  // Stack (S) register.
  private int $regS;

  // Registers A,X,Y.
  private int $regA;
  private int $regX;
  private int $regY;

  public function __construct(DataBus $dataBus) {
    $this->dataBus = $dataBus;
  }

  public function reset(int $maxCycles): void {
    // Get the reset vector.
    $this->regPC = 0xfffc;
    $this->cycles = 1;
    $this->jmp($this->readAddress());
    $this->loop($maxCycles);
  }

  /**
   * Main program loop.
   *
   * @uses lda()
   * @uses ldx()
   * @uses txs()
   * @uses sta()
   * @uses ror()
   * @uses jmp()
   * @uses jsr()
   * @uses pha()
   * @throws \Exception
   */
  public function loop(int $maxCycles): void {
    do {
      $opCode = $this->readByte();
      if (!array_key_exists($opCode, self::$opMatrix)) {
        throw new Exception('Unknown OpCode ' . Util::byteHex($opCode));
      }
      $operand = null;
      $instruction = self::$opMatrix[$opCode];
      switch ($instruction[1]) {
        case '#':
          $operand = $this->readByte();
          break;
        case 'a':
          $operand = $this->readAddress();
          break;
        case 'A':
          $operand = $this->regA;
          break;
        case 's':
          $operand = $this->regS;
          break;
        case 'i':
          $operand = null;
          break;
      }
      $function = $instruction[0];
      $this->$function($operand);
      if ($operand === null) {
        $this->readByte();
        $this->regPC--;
      }
    }
    while ($this->cycles < $maxCycles);
  }

  private function lda(int $operand): void {
    $this->regA = $operand;
  }

  private function ldx(int $operand): void {
    $this->regX = $operand;
  }

  private function txs($operand): void {
    // Stack starts at 0x0100
    $this->regS = 256 + $this->regX;
  }

  private function sta(int $operand): void {
    $this->write($operand, $this->regA);
  }

  private function ror(int $operand): void {
    $binary = Util::decBin($operand, 8);
    $this->regA = bindec(substr($binary, -1).substr($binary, 0, -1));
  }

  private function jmp(int $operand): void {
    $this->regPC = $operand;
  }

  /**
   * Jump to SubRoutine.
   *
   * @param int $operand
   *
   * @throws \Exception
   */
  private function jsr(int $operand): void {
    $return_address = $this->regPC + 2;
    $addressHi = (int) floor($return_address / 256);
    $addressLo = $return_address - $addressHi * 256;
    // @todo this isn't quite right, it looks like jsr actually only reads half of the address operand before reading the stack?
    $this->readByte($this->regS);
    $this->write($this->regS, $addressHi);
    $this->regS--;
    $this->write($this->regS, $addressLo);
    $this->regS--;
    $this->regPC = $operand;
  }

  /**
   * PusH Accumulator on stack.
   *
   * @param int $operand
   *
   * @return void
   * @throws \Exception
   */
  private function pha(int $operand): void {
    $this->write($operand, $this->regA);
    $this->regS--;
  }

  private function readByte(int $address = null): int {
    $address = $address ?: $this->regPC;
    $data = $this->dataBus->read($address);
    if (getenv('DP6502_DEBUG')) {
      // @todo PHPUnit test to verify output
      echo 'Read ' . Util::addressHex($this->regPC) . ': ' . Util::byteHex($data) . "\n";
    }
    $this->regPC++;
    $this->cycles++;
    return $data;
  }

  private function readAddress(): int {
    $addressLo = $this->readByte();
    $addressHi = $this->readByte();
    return $addressHi * 256 + $addressLo;
  }

  /**
   * @param int $address
   * @param int $data
   *
   * @throws \Exception
   */
  private function write(int $address, int $data): void {
    if (getenv('DP6502_DEBUG')) {
      echo 'Write ' . Util::addressHex($address) . ': ' . Util::byteHex($data) . "\n";
    }
    try {
      $this->dataBus->write($address, $data);
      $this->cycles++;
    }
    catch (Exception $exception) {
      throw new Exception('Could not write: ' . $exception->getMessage());
    }
  }

}
