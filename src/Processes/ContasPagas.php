<?php


namespace Figrana\Processes;

use Figrana\DBase;
use ApiGranatum\Granatum;
use ApiGranatum\Connector;
use ApiGranatum\Factories\Lancamentos as ApiLanc;
use ApiGranatum\Factories\Contas as ApiContas;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ContasPagas 
{
    public $conn;
    protected $db;
    protected $dbh;
    protected $logger;
    
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
            $_ENV['DB1_CONNECTION'],
            $_ENV['DB1_HOST'],
            $_ENV['DB1_PORT'],
            $_ENV['DB1_DATABASE']
        );
        $this->dbh = $this->db->connect(
            $_ENV['DB1_USERNAME'],
            $_ENV['DB1_PASSWORD']
        );
        if (!empty($logger)) {
            $this->logger = $logger;
        }
        $this->listContas();
    }
    
    public function find()
    {
        $cc = [];
    }
    
    protected function listContas()
    {
        $contas = new ApiContas($this->conn);
        $resp = $contas->all();
        echo "<PRE>";
        print_r($resp);
        echo "</PRE>";
    }
}
