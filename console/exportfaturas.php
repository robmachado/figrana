<?php
require_once '../vendor/autoload.php';

use Granatum;

/* 
 * Procura por faturas no legado, com base nos ultimo envio registrado
 * - compara com os dados já enviados na base local
 * - se já enviado PULA
 * - se não cria ARRAY com as faturas a enviar
 * - usa a API para enviar
 * - em caso de sucesso grava os envios na base local
 */

