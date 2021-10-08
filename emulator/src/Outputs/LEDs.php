<?php


namespace Danepowell\dp6502\Outputs;


use Danepowell\dp6502\Util;

class LEDs extends AbstractOutput {
  public function write (int $data): void {
    echo Util::decBin($data, 8) . "\n";
  }
}