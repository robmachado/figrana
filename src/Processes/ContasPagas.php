<?php

namespace Figrana\Processes;

use Figrana\DBase;
use ApiGranatum\Granatum;
use ApiGranatum\Connector;
use ApiGranatum\Factories\Lancamentos as ApiLanc;
use ApiGranatum\Factories\Contas as ApiContas;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Carbon\Carbon;
use Figrana\Aux\TimePeriod;

class ContasPagas 
{
    public $conn;
    protected $db;
    protected $dbh;
    protected $logger;
    protected $contas;

    public function __construct(Connector $conn = null, Logger $logger = null)
    {
        if (empty($conn)) {
            $token = $_ENV['GRANATUM_TOKEN'];
            $version = $_ENV['GRANATUM_VERSION'];
            $uri = $_ENV['GRANATUM_URI'];
            $conn = new Connector($token, $version, $uri);
        }
        $this->conn = $conn;
        $this->db = new DBase(
            $_ENV['DB2_CONNECTION'],
            $_ENV['DB2_HOST'],
            $_ENV['DB2_PORT'],
            $_ENV['DB2_DATABASE']
        );
        $this->dbh = $this->db->connect(
            $_ENV['DB2_USERNAME'],
            $_ENV['DB2_PASSWORD']
        );
        $this->logger = $logger;
        if (empty($logger)) {
            $this->logger = new Logger('Figrana');
            $real = realpath(dirname(__FILE__) . "/../../storage");
            $this->logger->pushHandler(
                new StreamHandler("$real/contaspagas.log", Logger::WARNING)
            );
        } 
        $this->contas = $this->listContas();
    }
    
    public function find()
    {
        $this->logger->warning('Iniciada a busca.');
        foreach ($this->contas as $conta) {
            $mark = strtoupper(substr($conta->descricao, 0, 4));
            switch ($mark) {
                case 'FDIC':
                    $this->getPagos($conta->id, 4);
                    break;
                case 'FIDC':
                    $this->getPagos($conta->id, 4);
                    break;
                case 'VIRT':
                    $this->getPagos($conta->id, 1);
                    break;
                case 'CART':
                    $this->getPagos($conta->id, 1);
                    break;
            }
        }
    }
    
    protected function getPagos($conta, $dif)
    {
        $dt = new Carbon();
        $dt->subMonth(1);
        $dataini = $dt->firstOfMonth()->format('Y-m-d');
        $dt = new Carbon();
        $dt->subDays($dif);
        $datafim = $dt->format('Y-m-d');
        $lanc = new ApiLanc($this->conn);
        $filtro = [
            'conta_id' => $conta,
            'data_inicio' => $dataini,
            'data_fim' => $datafim
        ];
        $tp = new TimePeriod();
        $list = json_decode($lanc->all($filtro));
        foreach ($list as $l) {
            $doc = $l->descricao;
            $cc = $l->centro_custo_lucro_id;
            $cat = $l->categoria_id;
            $valor = $l->valor;
            $dtVenc = $l->data_vencimento;
            $dtPagto = $l->data_pagamento;
            //vendas, com categoria de receitas de vendas, com data de pagamento
            //e com data de vencimento menor ou igula a data de corte
            if ($cc == '92047' && 
                ($cat == '790285' || $cat == '790285' || $cat == '790603') &&
                !empty($dtPagto) && ($dtVenc <= $datafim)    
            ){
                $d = explode(' ', $doc);
                $dup = substr($doc, 0,1) == 'V' ? 'V'.$d[2] : $d[2];
                $sqlComm = "SELECT id_conta, valor_pago FROM contas WHERE num_documento = '$dup'";
                $resp = $this->db->querySQL($this->dbh, $sqlComm);
                if (!empty($resp)) {
                    $id = $resp[0]['id_conta'];
                    //se não foi lançado o pagamento então lançar
                    if (empty($resp[0]['valor_pago'])) {
                        $sqlComm = "UPDATE contas SET valor_pago = '$valor', data_pagamento = data_vencimento WHERE id_conta = '$id'";
                        if (!$this->db->execSQL($this->dbh, $sqlComm)) {
                            $this->logger->error('Falha ao gravar na base de dados tabela contas.');
                        }
                    }    
                }
            }
        }
    }


    protected function listContas()
    {
        $contas = new ApiContas($this->conn);
        return json_decode($contas->all());
    }
}
