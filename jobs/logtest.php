<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once '../bootstrap.php';

use Figrana\Aux\LocalLog;

$log = new LocalLog();

$log->alert('teste 1');
$log->error('teste 2');
$log->alert('teste 3');
