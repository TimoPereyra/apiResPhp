<?php   

class Log
{

public function __construct($path)
{   
    $this->path     = $path; // ;
    $this->filename = "registro";
    $this->ip       = ($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 0;
}


public function insert($text)
{
    $tt =  time();
    $dt = new DateTime("@$tt");  // convert UNIX timestamp to PHP DateTime
    file_put_contents($this->path . $this->filename .".log", $dt->format('Y-m-d H:i:s') ." -> IP:". $this->ip." -> ". $text ."\n" , FILE_APPEND );
    
}

public function read()
{
    echo file_get_contents($this->path . $this->filename .".log");
}

}


?>