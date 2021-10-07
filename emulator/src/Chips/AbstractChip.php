<?php


namespace Danepowell\dp6502\Chips;


abstract class AbstractChip {
  abstract public function addressStart(): int;
  abstract public function addressEnd(): int;
  abstract public function read(int $address): int;
  abstract public function write(int $address, int $data): void;
}