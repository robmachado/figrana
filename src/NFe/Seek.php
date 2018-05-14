<?php

namespace Figrana\NFe;

use NFePHP\NFe\Common\Standardize;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

class Seek
{
    public $filesystem;
    
    public function __construct($path = null)
    {
        if (empty($path)) {
            $path = $_ENV['NFE_PATH'];
        }
        $adapter = new Local($path);
        $this->filesystem = new Filesystem($adapter);
    }
    
    public function getStd($chave)
    {
        $std = new \stdClass();
        $subfolder = '20' . substr($chave, 2, 4);
        $path = "$subfolder/$chave-nfe.xml";
        if (!$this->filesystem->has($path)) {
            return $std;
        }
        $xml = $this->filesystem->read($path);
        $s = new Standardize($xml);
        $std = $s->toStd();
        return $std;
    }
    
    public function listFiles($subpath)
    {
        $contents = $this->filesystem->listContents($subpath, false);
        $resp = [];
        foreach($contents as $c) {
            $c = json_decode(json_encode($c));
            if ($c->type == 'file' && $c->extension == 'xml') {
                $resp[] = $c->basename;
            } 
        }
        return $resp;
    }
}
