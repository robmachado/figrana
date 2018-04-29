<?php

namespace Figrana\Processes;

use ApiGranatum\Connector;
use ApiGranatum\Granatum;

class Parceiros
{
    const FORNECEDOR = 'F';
    const CLIENTE = 'C';
    
    private $conn;
    public $fornecedor;
    public $cliente;
    
    public function __construct()
    {
        $token = $_ENV['GRANATUM_TOKEN'];
        $version = $_ENV['GRANATUM_VERSION'];
        $uri = $_ENV['GRANATUM_URI'];
        $this->conn = new Connector($token, $version, $uri);
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
