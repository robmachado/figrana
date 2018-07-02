<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once __DIR__ . '/../bootstrap.php';

use NFePHP\NFe\Common\Standardize;
use Figrana\Processes\ContasPagas;

$cp = new ContasPagas();

$cp->find();
exit;

