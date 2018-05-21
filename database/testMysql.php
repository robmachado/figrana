<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once __DIR__ . '/../bootstrap.php';

use Figrana\DBase;

try {
    $db = new DBase(
        $_ENV['DB1_CONNECTION'],
        $_ENV['DB1_HOST'],
        $_ENV['DB1_PORT'],
        $_ENV['DB1_DATABASE']
    );
    $dbh = $db->connect(
        $_ENV['DB1_USERNAME'],
        $_ENV['DB1_PASSWORD']
    );  
    
    $sqlComm = "SELECT FROM lancamentos;";
    $resp = $db->querySQL($dbh, $sqlComm);
    if (empty($resp)) {
        echo 0;
    }
    echo "<pre>";
    print_r($resp);
    echo "</pre>";
} catch (\Exception $e) {
    echo $e->getMessage();
}  