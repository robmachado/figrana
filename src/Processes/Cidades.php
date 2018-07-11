<?php

namespace Figrana\Processes;

use ApiGranatum\Connector;
use ApiGranatum\Granatum;
use NFePHP\Common\Strings as StrCommon;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Cidades 
{
    public $conn;
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
    
    public function find($estado_id, $nome)
    {
        $filtro = ['estado_id' => $estado_id];
        $resp = Granatum::cidades($this->conn)->all($filtro);
        
        $nome = preg_replace('/\s+/', ' ',$nome);
        $nome = strtolower(trim($nome));
        $nome = StrCommon::replaceSpecialsChars($nome);
        $cidades = json_decode($resp);
        foreach ($cidades as $cidade) {
            $xnome = preg_replace('/\s+/', ' ',$cidade->nome);
            $xnome = strtolower(trim($xnome));
            $xnome = StrCommon::replaceSpecialsChars($xnome);
            if ($nome == $xnome) {
                return $cidade->id;
            }
        }
        return 0;
    }
}
