<?php

namespace Danepowell\dp6502;

/**
 * Address (16-bit) data type.
 */
class Address extends Bits {
  protected function length(): int {
    return 16;
  }
}
