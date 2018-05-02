<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once '../bootstrap.php';

use NFePHP\NFe\Common\Standardize;
use ApiGranatum\Granatum;
use ApiGranatum\Connector;
use Figrana\Processes\Entradas;
use Figrana\NFe\Seek;

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
$id = !empty($_POST['id']) ? $_POST['id'] : null;

//$chave = "35180404728183000117550000008689871927943207";
//$chave = "35180415179682002243550010002061521770468448";

if (empty($chave) && empty($id)) {
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
} elseif (!empty($chave) && empty($id)) {
    $chave = preg_replace("/[^0-9]/", "", $chave);
    $see = new Seek();
    $std = $see->getStd($chave);
    
    $entra = new Entradas();
    $entra->read($std);
    $fornec = json_decode($entra->parceiros->dados);
    $fornec = $fornec[0];
    $dups = json_decode(json_encode($entra->dups));
    $competencia = $entra->competencia;
    
    $template = file_get_contents('template.html');
    $template = str_replace('{{ template_title }}', 'Recebimento Fiscal', $template);
    
    $form = "<h1>Recebimento Fiscal</h1>
        <form method=\"POST\" action=\"gravar_recebimento.php\">
        <input type=\"hidden\" id=\"id\" name=\"id\" value=\"$fornec->id\">
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
                    <select class=\"selectpicker\" id=\"categoria\" name=\"categoria\">
                        <optgroup label=\"Picnic\">
                            <option>Mustard</option>
                            <option>Ketchup</option>
                            <option>Relish</option>
                        </optgroup>
                        <optgroup label=\"Camping\">
                            <option>Tent</option>
                            <option>Flashlight</option>
                            <option>Toilet Paper</option>
                        </optgroup>
                    </select>
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
                    <input type='text' class=\"form-control\" id=\"desc[]\" name=\"desc[]\" value=\"$dup->descricao\"/>
                </div>
            </div>
            <div class=\"col-md-3\">
                <label for=\"valor\">Valor</label>
                <div class=\"form-group\">
                    <input type='text' class=\"form-control\" id=\"valor[]\" name=\"valor[]\" value=\"$dup->valor\"/>
                </div>
            </div>
            <div class=\"col-md-3\">
                <label for=\"venc\">Vencimento</label>
                <div class=\"form-group\">
                    <input type='text' class=\"form-control\" id=\"venc[]\" name=\"venc[]\" value=\"$dup->data_vencimento\"/>
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

