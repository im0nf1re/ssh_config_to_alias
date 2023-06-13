<?php

require_once 'Config.php';

use App\Config;
if (isset($argv[1])) {
    $config = new Config($argv[1]);
    $config->makeAliases();
} else {
    echo "first argument should be a path to ssh config!\n";
}
