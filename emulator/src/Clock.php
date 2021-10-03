<?php

namespace Danepowell\dp6502;

class Clock {
  private float $period;

  public function __construct(float $period) {
   $this->period = $period;
  }

  public function tick(): void {
    time_sleep_until(microtime(true) + $this->period);
  }
}