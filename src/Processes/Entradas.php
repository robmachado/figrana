<?php

namespace Figrana\Processes;

use NFePHP\NFe\Common\Standardize;
use Figrana\Aux\Strings;
use Figrana\Processes\Parceiros;
use Carbon\Carbon;

class Entradas
{
    public $dups;
    public $parceiros;
    protected $uf;

    public function __construct()
    {
        $this->parceiros = new Parceiros();
        $this->getEstados();
    }
    
    public function read($std)
    {
        $cnpj = $std->NFe->infNFe->emit->CNPJ;
        if ($cnpj == '58716523000119') {
            return [];
        }
        $dt = Carbon::createFromFormat('Y-m-d\TH:i:sP', $std->NFe->infNFe->ide->dhEmi);
        $competencia = $dt->format('Y-m-d');
        $data = [
            'nome' => $std->NFe->infNFe->emit->xNome,
            'nome_fantasia' => !empty($std->NFe->infNFe->emit->xFant) ? $std->NFe->infNFe->emit->xFant : null,
            'documento' => $std->NFe->infNFe->emit->CNPJ,
            'inscricao_estadual' => $std->NFe->infNFe->emit->IE,
            'telefone' => !empty($std->NFe->infNFe->emit->enderEmit->fone) ? $std->NFe->infNFe->emit->enderEmit->fone : null,
            'endereco' => $std->NFe->infNFe->emit->enderEmit->xLgr,
            'endereco_numero' => $std->NFe->infNFe->emit->enderEmit->nro,
            'endereco_complemento' => !empty($std->NFe->infNFe->emit->enderEmit->xCpl) ? $std->NFe->infNFe->emit->enderEmit->xCpl : null,
            'bairro' => $std->NFe->infNFe->emit->enderEmit->xBairro,
            'cep' => Strings::mask("#####-###", $std->NFe->infNFe->emit->enderEmit->CEP)
        ];
        $id = $this->parceiros->findOrAdd($data, 'F');
        
        $cobr = $std->NFe->infNFe->cobr;
        $nDup = count($std->NFe->infNFe->cobr->dup);
        foreach ($std->NFe->infNFe->cobr->dup as $dup) {
            $this->dups[] = [
                'descricao' => '',
                'conta_id' => 0,
                'categoria_id' => 0,
                'valor' => $dup->vDup,
                'data_vencimento' => $dup->dVenc,
                'data_pagamento' => null,
                'data_competencia' => $competencia,
                'centro_custo_lucro_id' => null,
                'forma_pagamento_id' => null, //duplicata
                'pessoa_id' => $id,
                'tipo_documento_id', 
                'observacao' => null
            ];
        }
        
    }
    
    /**
     * retorna as categorias cadastradas
     * 
     */
    public function categorias()
    {
        
    }
    
    protected function getEstados()
    {
        $ufs = file_get_contents('../storage/estados.json');
        $std = json_decode($ufs);
        foreach($std as $uf) {
            $this->uf[$uf->sigla] = $uf->id;
        }
    }
    
    
    
    public function fornecedor(array $fornecedor)
    {
        //verifica se fornecedor já existe
        $resp = $this->fornec->all(['documento' => $fornecedor['documento']]);
        $resp = json_decode($resp);
        if (empty($resp)) {
            //cria o novo fornecedor
            $resp = $this->fornec->add($fornecedor);
        }
        $std = json_decode($resp);
        return $std->id;
    }
    
    protected function mask($mask, $str)
    {
        $str = str_replace(" ", "", $str);
        for($i=0; $i < strlen($str); $i++){
            $mask[strpos($mask,"#")] = $str[$i];
        }
        return $mask;
    }
}
