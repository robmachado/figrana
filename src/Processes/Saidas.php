<?php

namespace Figrana\Processes;

use NFePHP\NFe\Common\Standardize;
use Figrana\Aux\Strings;
use Figrana\Processes\Parceiros;
use Figrana\Processes\Cidades;
use ApiGranatum\Connector;
use ApiGranatum\Granatum;
use ApiGranatum\Factories\Lancamentos;
use Carbon\Carbon;
use DOMDocument;
use Figrana\DBase;

class Saidas
{
    public $dups;
    public $parceiros;
    public $competencia;
    protected $uf;
    protected $cidades;
    public $conn;
    protected $dbmsql;
    protected $dbhmsql;
    protected $db;
    protected $dbh;
    
    public function __construct()
    {
        $token = $_ENV['GRANATUM_TOKEN'];
        $version = $_ENV['GRANATUM_VERSION'];
        $uri = $_ENV['GRANATUM_URI'];
        $this->conn = new Connector($token, $version, $uri);
        $this->parceiros = new Parceiros($this->conn);
        $this->cidades = new Cidades($this->conn);
        $this->getEstados();
        $this->dbmsql = new DBase(
            $_ENV['DB2_CONNECTION'],
            $_ENV['DB2_HOST'],
            $_ENV['DB2_PORT'],
            $_ENV['DB2_DATABASE']
        );
        $this->dbhmsql = $this->dbmsql->connect(
            $_ENV['DB2_USERNAME'],
            $_ENV['DB2_PASSWORD']
        );
        
        $this->db = new DBase(
            $_ENV['DB1_CONNECTION'],
            $_ENV['DB1_HOST'],
            $_ENV['DB1_PORT'],
            $_ENV['DB1_DATABASE']
        );
        $this->dbh = $this->db->connect(
            $_ENV['DB1_USERNAME'],
            $_ENV['DB1_PASSWORD']
        );
    }
    
    public function read($std)
    {
        $cnpj = $std->NFe->infNFe->emit->CNPJ;
        if ($cnpj != '58716523000119') {
            return [];
        }
        $this->dups = [];
        $uf = $std->NFe->infNFe->dest->enderDest->UF;
        $xmun = $std->NFe->infNFe->dest->enderDest->xMun;
        $nnf = $std->NFe->infNFe->ide->nNF;
        $estado_id = $this->uf[$uf];
        $cidade_id = $this->cidades->find($estado_id, $xmun);
        $dt = Carbon::createFromFormat('Y-m-d\TH:i:sP', $std->NFe->infNFe->ide->dhEmi);
        $this->competencia = $dt->format('Y-m-d');
        $data = [
            'nome' => $std->NFe->infNFe->dest->xNome,
            'nome_fantasia' => !empty($std->NFe->infNFe->dest->xNome) ? $std->NFe->infNFe->dest->xNome : null,
            'documento' => !empty($std->NFe->infNFe->dest->CNPJ) ? $std->NFe->infNFe->dest->CNPJ : $std->NFe->infNFe->emit->CPF,
            'inscricao_estadual' => !empty($std->NFe->infNFe->dest->IE) ? $std->NFe->infNFe->dest->IE : '',
            'telefone' => !empty($std->NFe->infNFe->dest->enderDest->fone) ? $std->NFe->infNFe->dest->enderDest->fone : null,
            'endereco' => $std->NFe->infNFe->dest->enderDest->xLgr,
            'endereco_numero' => $std->NFe->infNFe->dest->enderDest->nro,
            'endereco_complemento' => !empty($std->NFe->infNFe->dest->enderDest->xCpl) ? $std->NFe->infNFe->dest->enderDest->xCpl : null,
            'bairro' => $std->NFe->infNFe->dest->enderDest->xBairro,
            'cep' => Strings::mask("#####-###", $std->NFe->infNFe->dest->enderDest->CEP),
            'estado_id' => $estado_id,
            'cidade_id' => $cidade_id
        ];
        $id = $this->parceiros->findOrAdd($data, 'C');
        
        $cobr = $std->NFe->infNFe->cobr;
        $n = count($std->NFe->infNFe->cobr->dup);
        
        if ($n == 1) {
            $this->dups[] = [
                'descricao' => "[$nnf] Dup. " . $std->NFe->infNFe->cobr->dup->nDup,
                'valor' => number_format($std->NFe->infNFe->cobr->dup->vDup, 2, '.', ''),
                'data_vencimento' => $std->NFe->infNFe->cobr->dup->dVenc
            ];
        } else {
            foreach ($std->NFe->infNFe->cobr->dup as $dup) {
                $this->dups[] = [
                    'descricao' => "[$nnf] Dup. " . $dup->nDup,
                    'valor' => number_format($dup->vDup, 2, '.', ''),
                    'data_vencimento' => $dup->dVenc
                ];
            }
        }
    }
    
    
    public function find($chave)
    {
        $sqlComm = "SELECT id FROM lancamentos WHERE chave='$chave'";
        $resp = $this->db->querySQL($this->dbh, $sqlComm);
        if (empty($resp)) {
            return false;
        }
        return true;
    }
    
    
    public function getStatusNFe($nNF)
    {
        $num = number_format($nNF + 100000, 0, '', '.'); 
        $sqlComm = "SELECT DISTINCT status from notas_fiscais_produtos where num_nf = '$num';";
        $resp = $this->dbmsql->querySQL($this->dbhmsql, $sqlComm);
        if (empty($resp)) {
            return 0;
        }
        return $resp[0][0];
    }
    
    protected function getEstados()
    {
        $ufs = file_get_contents(__DIR__ . '/../../storage/estados.json');
        $std = json_decode($ufs);
        foreach($std as $uf) {
            $this->uf[$uf->sigla] = $uf->id;
        }
    }
}
