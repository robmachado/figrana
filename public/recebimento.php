<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once '../bootstrap.php';

use NFePHP\NFe\Common\Standardize;
use ApiGranatum\Granatum;
use ApiGranatum\Connector;
use Figrana\Processes\Entradas;
use Figrana\Processes\Lancamentos;
use Figrana\NFe\Seek;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$logger = new Logger('Figrana');
$logger->pushHandler(
    new StreamHandler(__DIR__ . "/../storage/recebimentos.log", Logger::WARNING)
);

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

$chave = !empty($_POST['chave']) ? $_POST['chave'] : null;
$pessoaid = !empty($_POST['id']) ? $_POST['id'] : null;
$fornecedor = !empty($_POST['fornecedor']) ? $_POST['fornecedor'] : null;
$categoria = !empty($_POST['categoria']) ? $_POST['categoria'] : null;
$competencia = !empty($_POST['competencia']) ? $_POST['competencia'] : null;
$dupdesc  = !empty($_POST['dupdesc']) ? $_POST['dupdesc'] : null;
$dupvalor  = !empty($_POST['dupvalor']) ? $_POST['dupvalor'] : null;
$dupvenc  = !empty($_POST['dupvenc']) ? $_POST['dupvenc'] : null;

if (empty($chave) && empty($pessoaid)) {
    $template = file_get_contents('template.html');
    $template = str_replace('{{ template_title }}', 'Recebimento Fiscal', $template);
    $form = "<h1>Recebimento Fiscal</h1>
        <form method=\"POST\" action=\"recebimento.php\">
        <div class=\"row\">
            <div class=\"col-md-2\"></div>
            <div class=\"col-md-8\">
                <label for=\"chave\">Chave NFe</label>
                <div class=\"form-group\">
                    <input type='text' class=\"form-control\" id=\"chave\" name=\"chave\" />
                </div>
            </div>
            <div class=\"col-md-2\"></div>
        </div>
        <div class=\"row\">
            <div class=\"col-md-2\"></div>
            <div class=\"col-md-8\">
                <button type=\"submit\" class=\"btn btn-primary\">Submit</button>
            </div>
            <div class=\"col-md-2\"></div>
        </div>
        </form>";
    $template = str_replace('{{ script }}', '', $template);
    $template = str_replace('{{ container }}', $form, $template);
    echo $template;
    die;
} elseif (!empty($chave) && empty($pessoaid)) {
    $chave = preg_replace("/[^0-9]/", "", $chave);
    $see = new Seek();
    $std = $see->getStd($chave);
    if (empty($std)) {
        echo "NFe não localizada.";
        die;
    }
    $lanc = new Lancamentos();
    if ($lanc->find($chave)) {
        echo "NFe já lançada no sistema.";
        die;
    }
    $entra = new Entradas();    
    $entra->read($std);
    $fornec = json_decode($entra->parceiros->dados);
    $dups = json_decode(json_encode($entra->dups));
    $competencia = $entra->competencia;
    $selCategorias = str_replace('', '', $entra->categorias());
    
    $template = file_get_contents('template.html');
    $template = str_replace('{{ template_title }}', 'Recebimento Fiscal', $template);
    
    $form = "<h1>Recebimento Fiscal</h1>
        <form method=\"POST\" action=\"recebimento.php\">
        <input type=\"hidden\" id=\"id\" name=\"id\" value=\"$fornec->id\">
        <input type=\"hidden\" id=\"chave\" name=\"chave\" value=\"$chave\">
        <div class=\"row\">
            <div class=\"col-md-2\"></div>
            <div class=\"col-md-3\">
                <label for=\"fonecedor\">Fornecedor</label>
                <div class=\"form-group\">
                    <input type='text' class=\"form-control\" id=\"fornecedor\" name=\"fornecedor\" value=\"$fornec->nome\"/>
                </div>
            </div>
            <div class=\"col-md-3\">
                <label for=\"categoria\">Categoria</label>
                <div class=\"form-group\">
                    $selCategorias
                </div>
            </div>
            <div class=\"col-md-3\">
                <label for=\"competencia\">Competencia</label>
                <div class=\"form-group\">
                    <input type='text' class=\"form-control\" id=\"competencia\" name=\"competencia\" value=\"$competencia\"/>
                </div>
            </div>
            <div class=\"col-md-2\"></div>
        </div>";
    
    foreach ($dups as $dup) {
        $form .= "<div class=\"row\">
            <div class=\"col-md-2\"></div>
            <div class=\"col-md-3\">
                <label for=\"desc\">Descrição</label>
                <div class=\"form-group\">
                    <input type='text' class=\"form-control\" id=\"dupdesc[]\" name=\"dupdesc[]\" value=\"$dup->descricao\"/>
                </div>
            </div>
            <div class=\"col-md-3\">
                <label for=\"valor\">Valor</label>
                <div class=\"form-group\">
                    <input type='text' class=\"form-control\" id=\"dupvalor[]\" name=\"dupvalor[]\" value=\"$dup->valor\"/>
                </div>
            </div>
            <div class=\"col-md-3\">
                <label for=\"venc\">Vencimento</label>
                <div class=\"form-group\">
                    <input type='text' class=\"form-control\" id=\"dupvenc[]\" name=\"dupvenc[]\" value=\"$dup->data_vencimento\"/>
                </div>
            </div>
            <div class=\"col-md-1\"></div>
        </div>";
    }
    
    $form .= "<div class=\"row\">
            <div class=\"col-md-2\"></div>
            <div class=\"col-md-8\">
                <button type=\"submit\" class=\"btn btn-primary\">Submit</button>
            </div>
            <div class=\"col-md-2\"></div>
        </div>
        </form>";
    $template = str_replace('{{ script }}', '', $template);
    $template = str_replace('{{ container }}', $form, $template);
    echo $template;
    die;
} else {
    $lanc = new Lancamentos(null, $logger);
    $alanc = [];
    $i = 0;
    foreach ($dupvalor as $valor) {
        $alanc[] = [
            'conta_id' => '64462', //carteira
            'categoria_id' => $categoria,
            'descricao' => 'Duplicata '. $dupdesc[$i],
            'centro_custo_lucro_id' => '96107', //producao
            'tipo_custo_nivel_producao_id' => 2, //custo variável
            'tipo_custo_apropriacao_produto_id' => 1, //custo direto
            'valor' => -1 * $valor,
            'data_vencimento' => $dupvenc[$i],
            'data_competencia' => $competencia,
            'pessoa_id' => 	$pessoaid,
            'tipo_documento_id' => '137283', //NF
            'forma_pagamento_id' => '288361', //Duplicatas
            'observacao' => $fornecedor
        ];
        $i++;
    }
    $lanc->save($chave, $alanc);
    header("Refresh: 3; url=recebimento.php");
    echo 'Sucesso!!!';
}


//buscar categorias granatum
//buscar fornecedor granatum
//buscar centros granatum

//se não tem fornecedor => 
/**
 * nome	Nome do fornecedor	Requerido
nome_fantasia		Opcional
documento	CNPJ da empresa	Opcional
inscricao_estadual	Inscrição Estadual da empresa	Opcional
telefone	Telefone da empresa	Opcional
email	Email da empresa	Opcional
endereco	Apenas logradouro. Ex: Rua 13 de maio.	Opcional
endereco_numero	Número do imóvel	Opcional
endereco_complemento	Complemento do endereço do imóvel	Opcional
bairro		Opcional
cep	Código postal do imóvel	Opcional
cidade_id	ID da cidade do Imóvel.	Opcional
estado_id	ID do estado do Imóvel. Verifique os códigos aqui.	Opcional
observacao		Opcional
cliente	Indica se o fornecedor cadastro é também um cliente. Valor booleano.
 */


/**
 * descricao	Descrição do lançamento	Requerido
conta_id	ID da conta bancária	Requerido
categoria_id	ID da categoria	Requerido
valor	Use negativo para despesa e positivo para receita. Ex.: -10.00 e 10.00	Requerido
data_vencimento	Data de vencimento do lançamento	Requerido
data_pagamento	Data do pagamento. Indica que o lançamento está pago	Opcional
data_competencia	Data da competência. Data que indica a efetiva data do recebimento.	Opcional
centro_custo_lucro_id	ID do centro de custo e lucro	Opcional
forma_pagamento_id	ID da forma de pagamento	Opcional
pessoa_id	ID do cliente no caso de Receita e ID do fornecedor no caso de Despesa	Opcional
tipo_documento_id	ID do tipo de documento	Opcional
total_repeticoes	Número de vezes que o lançamento será repetido	Opcional
observacao	Observação do lançamento	Opcional
itens_adicionais[]	Itens adicionais para criar lançamento composto	Opcional
 */

