<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once '../bootstrap.php';

use NFePHP\NFe\Common\Standardize;
use ApiGranatum\Granatum;
use ApiGranatum\Connector;
use Figrana\Processes\Lancamentos;
use Figrana\NFe\Seek;
use Figrana\Processes\Saidas;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

$anomes = date('Ym');
$path = "/var/www/nfe/producao/enviadas/aprovadas";

$seek = new Seek($path);
$resp = $seek->listFiles($anomes);

$saidas = new Saidas();
foreach ($resp as $file) {
    $chave = preg_replace("/[^0-9]/", "",$file);
    $std = $seek->getStd($chave);
    //verificar se tem cobrança
    //se não continue 
    if (empty($std->NFe->infNFe->cobr)) {
        continue;
    }
    //verificar se está cancelada 
    
    //verificar se já foi lançada
    $nNF = $std->NFe->infNFe->ide->nNF;
    $resp = $saidas->getStatusNFe($nNF);
    echo "<pre>";
    print_r($resp);
    echo "</pre>";
    die;
}

echo "<pre>";
print_r($resp);
echo "</pre>";


