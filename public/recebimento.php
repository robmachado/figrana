<?php

require_once '../vendor/autoload.php';



/* 
 * Entrada de NFe fornecedores
 * campo chave (ler com leitor de barras)
 * 
 * - localizar xml no servidor
 * - carregar o xml, converter para stdClass
 * - coletar dados
 *    - data emissão
 *    - duplicatas: valor e data de vencimento
 *    - verificar se já não foi gravado na base local
 *    - se sim avisar e sair
 *    - se não continuar
 *    - buscar na API granatum o plano de contas
 *    - selecionar o plano de contas
 *    - ao enviar gravar em base local, no legado (se necessário)
 *    - passar o envio para a API 
 * 
 */

