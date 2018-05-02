<?php

namespace Figrana\Processes;

use NFePHP\NFe\Common\Standardize;
use Figrana\Aux\Strings;
use Figrana\Processes\Parceiros;
use Figrana\Processes\Cidades;
use Carbon\Carbon;

class Entradas
{
    public $dups;
    public $parceiros;
    public $competencia;
    protected $uf;
    protected $cidades;
    protected $conn;


    public function __construct()
    {
        $token = $_ENV['GRANATUM_TOKEN'];
        $version = $_ENV['GRANATUM_VERSION'];
        $uri = $_ENV['GRANATUM_URI'];
        $this->conn = new Connector($token, $version, $uri);
        $this->parceiros = new Parceiros();
        $this->cidades = new Cidades();
        $this->getEstados();
        
    }
    
    public function read($std)
    {
        $cnpj = $std->NFe->infNFe->emit->CNPJ;
        if ($cnpj == '58716523000119') {
            return [];
        }
        $uf = $std->NFe->infNFe->emit->enderEmit->UF;
        $xmun = $std->NFe->infNFe->emit->enderEmit->xMun;
        $estado_id = $this->uf[$uf];
        $cidade_id = $this->cidades->find($estado_id, $xmun);
        $dt = Carbon::createFromFormat('Y-m-d\TH:i:sP', $std->NFe->infNFe->ide->dhEmi);
        $this->competencia = $dt->format('Y-m-d');
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
            'cep' => Strings::mask("#####-###", $std->NFe->infNFe->emit->enderEmit->CEP),
            'estado_id' => $estado_id,
            'cidade_id' => $cidade_id
        ];
        $id = $this->parceiros->findOrAdd($data, 'F');
        
        $cobr = $std->NFe->infNFe->cobr;
        $n = count($std->NFe->infNFe->cobr->dup);
        if ($n == 1) {
            $this->dups[] = [
                'descricao' => $dup->nDup,
                'valor' => -1 * $dup->vDup,
                'data_vencimento' => $dup->dVenc
            ];
        } else {
        foreach ($std->NFe->infNFe->cobr->dup as $dup) {
            $this->dups[] = [
                'descricao' => $dup->nDup,
                'valor' => -1 * $dup->vDup,
                'data_vencimento' => $dup->dVenc
            ];
        }
        }
        
    }
    
    /**
     * retorna as categorias cadastradas
     * 
     */
    public function categorias()
    {
        $cats = json_decode(Granatum::categorias($this->conn)->all());
        
    foreach ($cats as $cat) {
        if (count($cat->caterorias_filhas) > 0) {
            //é <optgroup label=\"Picnic\">
            
        }
    }
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
