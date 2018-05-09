<?php

namespace Figrana\Processes;

use ApiGranatum\Connector;
use ApiGranatum\Granatum;

class Parceiros
{
    const FORNECEDOR = 'F';
    const CLIENTE = 'C';
    
    private $conn;
    public $dados;
    
    public function __construct(Connector $conn = null)
    {
        if (empty($conn)) {
            $token = $_ENV['GRANATUM_TOKEN'];
            $version = $_ENV['GRANATUM_VERSION'];
            $uri = $_ENV['GRANATUM_URI'];
            $conn = new Connector($token, $version, $uri);
        }
        $this->conn = $conn;
    }
    
    public function findOrAdd($data, $type)
    {
        if ($type === self::FORNECEDOR) {
            $parceiro = Granatum::fornecedores($this->conn);
        } elseif ($type == self::CLIENTE) {
            $parceiro = Granatum::clientes($this->conn);
        }
        if (!array_key_exists('documento', $data)) {
            return '';
        }
        $filtro = ['documento' => $data['documento']];
        $resp = $parceiro->all($filtro);
        if (empty(json_decode($resp))) {
            //não localizado então incluir
            $resp = $parceiro->add($data);
        }
        $this->dados = $resp;
        $std = json_decode($resp);
        if (!empty($std)) {
           if (is_array($std)) {
               $std = $std[0];
           }
           if (!empty($std->id)) {
               return $std->id;
           }
       }
    }
}
