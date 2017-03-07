<?php
/**
 * Copyright (c) University of Liverpool. All rights reserved.
 * @author Andrew Collins
 */
use pgb_liv\mascot_monitor\MascotMonitor;
use pgb_liv\php_ms\Search\MascotSearch;

error_reporting(E_ALL);
ini_set('display_errors', true);

require_once 'conf/config.php';
require_once 'conf/autoload.php';
require_once 'vendor/autoload.php';

$lockDir = SAVE_PATH . '/.mascotMonitor';
$lockFile = $lockDir . '/.lock';

if (! file_exists($lockFile)) {
    if (! is_dir($lockDir)) {
        mkdir($lockDir);
    }
    
    touch($lockFile);
}

$lock = fopen($lockFile, 'r+');

if (! flock($lock, LOCK_EX | LOCK_NB)) {
    die('Process Running. Terminating.');
}

$mascot = new MascotSearch(MASCOT_HOST, MASCOT_PORT, MASCOT_PATH);

if (! $mascot->authenticate(MASCOT_USER, MASCOT_PASS)) {
    die('ERROR: Bad username or password' . PHP_EOL);
}

$monitor = new MascotMonitor($mascot);
$monitor->saveResults(SAVE_PATH . '/results');

fclose($lock);
