<?php


namespace Danepowell\dp6502\Outputs;


abstract class AbstractOutput {
  abstract public function write(int $bits): void;
}