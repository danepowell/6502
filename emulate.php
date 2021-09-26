<?php

$program = array_values(unpack('C*', file_get_contents($argv[1])));
$programHex = [];
$programBin = [];
foreach ($program as $op) {
  $programHex[] = str_pad(base_convert($op, 10, 16), 2, '0');
  $programBin[] = str_pad(base_convert($op, 10, 2), 8, '0');
}

$startAddr = $programBin[0x7ffd] . $programBin[0x7ffc];
