#!/usr/bin/env php
<?php
require_once __DIR__ . '/../src/autoload.php';

use App\Cron\RssImportCommand;

$cmd = new RssImportCommand();
$code = $cmd->run();
exit($code);
