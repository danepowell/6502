<?php


namespace Danepowell\dp6502;


class Util {
  public static function validateAddress(int $address): void {
    self::validateBitString($address, 16);
  }

  public static function validateByte(int $byte): void {
    self::validateBitString($byte, 8);
  }

  public static function addressHex(int $bits): string {
    return self::decHex($bits, 4);
  }

  public static function byteHex(int $bits): string {
    return self::decHex($bits, 2);
  }

  private static function decHex(int $bits, int $length): string {
    return str_pad(dechex($bits), $length, '0', STR_PAD_LEFT);
  }

  private static function validateBitString(int $bits, $length): void {
    if ($bits > 2**$length) {
      throw new \Exception("Data $bits exceeds allowed size of $length bits");
    }
  }
}