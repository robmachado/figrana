<?php
require_once '../bootstrap.php';

use ApiGranatum\Connector;
use ApiGranatum\Granatum;

$token = $_ENV['GRANATUM_TOKEN'];
$version = $_ENV['GRANATUM_VERSION'];
$uri = $_ENV['GRANATUM_URI'];

try {
    $conn = new Connector($token, $version, $uri);
    $resp = Granatum::categorias($conn)->get(790247);
    if ($resp === 'false') {
        echo "Fracasso. Falhou !";
    } elseif ($resp === 'true') {
            echo "Sucesso!!";
    } else {
        $std = json_decode($resp);
        echo "<pre>";
        print_r($std);
        echo "</pre>";
        foreach($std->categorias_filhas as $child) {
            $d[] = $child->descricao;
        }
        echo "<pre>";
        print_r($d);
        echo "</pre>";
    }
} catch (\Exception $e) {
    echo $e->getMessage();
}

/* 
 * Procura por faturas no legado, com base nos ultimo envio registrado
 * - compara com os dados já enviados na base local
 * - se já enviado PULA
 * - se não cria ARRAY com as faturas a enviar
 * - usa a API para enviar
 * - em caso de sucesso grava os envios na base local
 */

