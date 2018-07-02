<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once __DIR__ . '/../bootstrap.php';

use Figrana\DBase;

$db = new DBase(
    $_ENV['DB2_CONNECTION'],
    $_ENV['DB2_HOST'],
    $_ENV['DB2_PORT'],
    $_ENV['DB2_DATABASE']
);
$dbh = $db->connect(
    $_ENV['DB2_USERNAME'],
    $_ENV['DB2_PASSWORD']
);

$sqlComm = "SELECT id_conta FROM contas WHERE num_documento = '50563-1'";

$resp = $db->querySQL($dbh, $sqlComm);

echo "<pre>";
print_r($resp);
echo "</pre>";