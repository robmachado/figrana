<?php
require_once '../bootstrap.php';

use Granatum\Estados;
use Granatum\Categorias;

$token = $_ENV['GRANATUM_TOKEN'];

$ufs = new Estados();
$ufs->setToken($token);
$ufs->all();
$error = $ufs->error();
$estados = $ufs->body();

$cats = new Categorias();
$cats->setToken($token);
$cats->all();
$categorias = $cats->body();

echo "<pre>";
echo "ERROR: $error <br>";
echo $categorias;
echo "</pre>";


/* 
 * Procura por faturas no legado, com base nos ultimo envio registrado
 * - compara com os dados já enviados na base local
 * - se já enviado PULA
 * - se não cria ARRAY com as faturas a enviar
 * - usa a API para enviar
 * - em caso de sucesso grava os envios na base local
 */

