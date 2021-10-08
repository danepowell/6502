<?php


namespace Danepowell\dp6502\Outputs;


use Danepowell\dp6502\Util;

class LEDs extends AbstractOutput {
  public function write (int $bits): void {
    echo Util::decBin($bits, 8) . "\n";
  }
}