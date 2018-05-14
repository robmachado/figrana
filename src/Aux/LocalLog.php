<?php


namespace Figrana\Aux;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

class LocalLog
{
    protected $filesystem;
    protected $file = 'log.log';
    protected $path;

    public function __construct()
    {
        
    }
    
    
    public function alert($contents)
    {
        $contents = 'ALERT: ['.date('Y-m-d H:i:s').'] ' . $contents;
        $response = $this->filesystem->update('log.log', $contents);
    }
    
    public function error($contents)
    {
        $contents = 'ERROR: ['.date('Y-m-d H:i:s').'] ' . $contents;
        $response = $this->filesystem->update('log.log', $contents);
    }
}
