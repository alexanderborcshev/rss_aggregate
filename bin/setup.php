#!/usr/bin/env php
<?php
require_once __DIR__ . '/../src/autoload.php';

use App\Setup\DatabaseSetupCommand;

$cmd = new DatabaseSetupCommand();
$code = $cmd->run($argv);
exit($code);
