<?php


namespace Danepowell\dp6502;


class Util {
  public static function validateAddress(int $address): void {
    self::validateBitString($address, 16);
  }

  public static function validateByte(int $byte): void {
    self::validateBitString($byte, 8);
  }

  private static function validateBitString(int $bits, $length): void {
    if ($bits > 2**$length) {
      throw new \Exception("Data $bits exceeds allowed size of $length bits");
    }
  }
}