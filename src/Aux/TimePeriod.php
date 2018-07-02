<?php

namespace Figrana\Aux;

use DateTime;
use DateInterval;
use Carbon\Carbon;

class TimePeriod
{
    public $dtIniDays = 0;
    public $dtFimDays = 0;
    public $dtIni = '';
    public $dtFim = '';
    public $mydate;
    
    public function __construct($anomes = '')
    {
        date_default_timezone_set('America/Sao_Paulo');
        if ($anomes != '') {
            $this->set($anomes);
        } else {
            $this->set(date('Ym'));
        }
    }
    
    public function set($anomes)
    {
        $year = substr($anomes, 0, 4);
        $month = substr($anomes, 4, 2);
        $dt0 = new DateTime('1900-01-01');
        $dt1 = new DateTime(
            $year
            . '-'
            . str_pad($month, 2, '0', STR_PAD_LEFT)
            . '-01'
        );
        $this->mydate = $dt1;
        $this->dtIni = $dt1->format('Y-m-d');
        $this->dtIniDays = self::toDays($this->dtIni);
        //adiciona um mês
        $int = new DateInterval('P1M');
        $dt1->add($int);
        $this->dtFim = $dt1->format('Y-m-d');
        $this->dtFimDays = self::toDays($this->dtFim);
        return $this;
    }
    
    public function add($date, $type, $value)
    {
        $invert = 0;
        if ($value < 0) {
            $invert = 1;
            $value = -1 * $value;
        }
        $data = new DateTime($date);
        switch (strtoupper($type)) {
            case 'D':
                $strInt = "P".$value."D";
                break;
            case 'M':
                $strInt = "P".$value."M";
                break;
            case 'M':
                $strInt = "P".$value."Y";
                break;
        }
        $int = new DateInterval($strInt);
        $int->invert = $invert;
        return $data->add($int);
    }
    
    public function toDays($data)
    {
        $ts = Carbon::createFromFormat('Y-m-d', $data)->timestamp;
        return (int) ($ts/86400) + 25569; 
    }
    
    public function toDate($number)
    {
        if (empty($number)) {
            return null;
        }
        $ts = ($number+1-25569)*86400;
        $dt = new DateTime();
        return $dt->setTimestamp($ts); 
    }
}
