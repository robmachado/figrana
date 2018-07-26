<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once __DIR__ . '/../bootstrap.php';

use NFePHP\NFe\Common\Standardize;
use ApiGranatum\Granatum;
use ApiGranatum\Connector;
use Figrana\Processes\Lancamentos;
use Figrana\NFe\Seek;
use Figrana\Processes\Saidas;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Carbon\Carbon;

$logger = new Logger('Figrana');
$logger->pushHandler(
    new StreamHandler(__DIR__ . "/../storage/faturamentos.log", Logger::WARNING)
);

$anomes = date('Ym');
$path = realpath($_ENV['NFE_PATH'] .'/../enviadas/aprovadas');
$seek = new Seek($path);
$resp = $seek->listFiles($anomes);

$saidas = new Saidas();
$lanc = new Lancamentos($saidas->conn, $logger);

foreach ($resp as $file) {
    
    $chave = preg_replace("/[^0-9]/", "",$file);
    echo "$chave <br>";
    $di = Carbon::now();
    $std = null;
    $std = $seek->getStd($chave);
    //verificar se tem cobrança, se tem não continue 
    if (empty($std->NFe->infNFe->cobr->dup) || $std->NFe->infNFe->ide->tpNF != 1) {
        echo "\n";
        continue;
    }
    //verificar se está cancelada 
    $cStat = $std->protNFe->infProt->cStat;
    if (in_array($cStat, ['101', '135', '155'])) {
        echo "\n";
        continue;
    }
    $nNF = $std->NFe->infNFe->ide->nNF;
    //verificar o status da NFe no sistema fimatec
    $status = $saidas->getStatusNFe($nNF);
    //verificar se já foi lançada para o granatum
    if ($saidas->find($chave)) {
        echo "\n";
        continue;
    }
    try {
        $saidas->read($std);
    } catch (\Exception $e) {
        $logger->error('SAIDAS read ' . $e->getMessage());
        echo "\n";
        continue;
    }    
    $cliente = json_decode($saidas->parceiros->dados);
    $pessoaid = $cliente->id;
    $competencia = $saidas->competencia;
    $dups = json_decode(json_encode($saidas->dups));
    $alanc = [];
    foreach ($dups as $dup) {
        //790286 venda de serviços
        //790285 vendas de produtos
        $alanc[] = [
            'conta_id' => '64462', //carteira
            'categoria_id' => '790285', //vendas de produtos
            'descricao' => ''. $dup->descricao,
            'centro_custo_lucro_id' => '92047', //vendas
            'valor' => $dup->valor,
            'data_vencimento' => $dup->data_vencimento,
            'data_competencia' => $saidas->competencia,
            'pessoa_id' => $cliente->id,
            'tipo_documento_id' => '137283', //NF
            'observacao' => $cliente->nome
        ];
        if ($status < 100 && $status > 0) {
            //64562 VIRTUAL
            $valor = round($dup->valor * (100/$status) - $dup->valor, 2); 
            $alanc[] = [
                'conta_id' => '64562', //VIRTUAL
                'categoria_id' => '790285', //vendas de produtos
                'descricao' => 'V'. $dup->descricao,
                'centro_custo_lucro_id' => '92047', //vendas
                'valor' => $valor,
                'data_vencimento' => $dup->data_vencimento,
                'data_competencia' => $saidas->competencia,
                'pessoa_id' => $cliente->id,
                'tipo_documento_id' => '137283', //NF
                'observacao' => $cliente->nome
            ];
        }
    }
    if ($lanc->save($chave, $alanc)) {
        $logger->warning("SUCESSO ... $chave -> gravada.");
    }
    $df = Carbon::now();
    sleep(1);
    echo " [" . $di->diffInSeconds($df) . "] \n"; 
}
