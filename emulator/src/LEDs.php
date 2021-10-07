<?php


namespace Danepowell\dp6502;


class LEDs {
  public function write (int $bits): void {
    echo Util::decBin($bits, 8) . "\n";
  }
}