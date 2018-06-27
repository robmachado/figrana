<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once __DIR__ . '/../bootstrap.php';

use NFePHP\NFe\Common\Standardize;
use ApiGranatum\Granatum;
use ApiGranatum\Connector;
use Figrana\Processes\ContasPagas;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Carbon\Carbon;


$logger = new Logger('Figrana');
$logger->pushHandler(
    new StreamHandler(__DIR__ . "/../storage/contaspagas.log", Logger::WARNING)
);

//filtar com atá tres dias atraz
$dt = new Carbon();
$dt->subDays(3);
$datafim = $dt->format('Y-m-d');

echo $datafim;

