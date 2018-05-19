<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once __DIR__ . '/../bootstrap.php';

use Figrana\DBase;

try {
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
    
    $sqlComm = "SELECT DISTINCT status from notas_fiscais_produtos where num_nf = '149.954';";
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