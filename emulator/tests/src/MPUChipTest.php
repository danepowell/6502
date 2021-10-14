<?php

namespace Danepowell\dp6502\Tests;

use Danepowell\dp6502\Chips\MPUChip;
use Danepowell\dp6502\Chips\RAMChip;
use Danepowell\dp6502\Chips\ROMChip;
use Danepowell\dp6502\Chips\VIAChip;
use Danepowell\dp6502\DataBus;
use Danepowell\dp6502\Outputs\LEDs;
use PHPUnit\Framework\TestCase;

class MPUChipTest extends TestCase {
  // Must be aligned to instruction.
  private static int $maxCycles = 53;

  public function testHelloWorld(): void {
    $serial_output = file_get_contents(__DIR__ . '/../fixture/hello-world.serial');
    $serial_output = explode("\n", $serial_output);
    $serial_output = array_slice($serial_output, 6, self::$maxCycles);
    $rom = new ROMChip(__DIR__ . '/../fixture/hello-world.out');
    $ram = new RAMChip();
    $leds = new LEDs();
    $via = new VIAChip($leds);
    $dataBus = new DataBus([$rom, $ram, $via]);
    $mpu = new MPUChip($dataBus);
    $mpu->reset(self::$maxCycles);
    $expectedOutput = '';
    foreach ($serial_output as $line) {
      $line = explode(' ', $line);
      $op = $line[3] === 'r' ? 'Read' : 'Write';
      $expectedOutput .= sprintf("%s %s: %s\n", $op, $line[2], $line[4]);
    }
    $this->expectOutputString($expectedOutput);
  }
}
