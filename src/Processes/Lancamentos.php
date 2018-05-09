<?php

namespace Figrana\Processes;

use Figrana\DBase;
use ApiGranatum\Granatum;
use ApiGranatum\Connector;
use ApiGranatum\Factories\Lancamentos as ApiLanc;

class Lancamentos
{
    protected $conn;
    protected $db;
    protected $dbh;

    public function __construct(Connector $conn = null)
    {
        if (empty($conn)) {
            $token = $_ENV['GRANATUM_TOKEN'];
            $version = $_ENV['GRANATUM_VERSION'];
            $uri = $_ENV['GRANATUM_URI'];
            $conn = new Connector($token, $version, $uri);
        }
        $this->conn = $conn;
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
    
    public function find($chave)
    {
        $resp = $this->db->querySQL($this->dbh, "SELECT * FROM lancamentos WHERE chave='$chave';",[]);
        if (empty($resp)) {
            return false;
        }
        return $resp;
    }
    
    public function save($chave, $dados)
    {
        $data = date('Y-m-d H:i:s');
        //gravar na base
        $sqlComm = "INSERT INTO lancamentos (chave, created_at) VALUES ("
                . "'$chave',"
                . "'$data');";
        if (!$this->db->execSQL($this->dbh, $sqlComm)) {
            echo "Falha na gravação na base de dados";
            die;
        }
        //gravar no granatum
        $lanc = new ApiLanc($this->conn);
        foreach ($dados as $d) {
            $resp = $lanc->add($d);
            $std = json_decode($resp);
            if (empty($std->id)) {
                echo "ERRO $resp";
                die;
            }
        }    
        return true;
    }
    
}
