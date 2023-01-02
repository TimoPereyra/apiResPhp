<?php



class BaseController
{

    private $pdo;

    public function __construct()
    {
        $this->pdo = (new Database())->connect();
    }

    //Obtener parametros para updates
    function getParams($input)
    {
        $filterParams = [];
        foreach($input as $param => $value)
        {
            $filterParams[] = "$param=:$param";
        }
        return implode(", ", $filterParams);
	}
    
    //Asociar todos los parametros a un sql
	function bindAllValues($statement, $params)
    {
		foreach($params as $param => $value)
        {
				$statement->bindValue(':'.$param, $value);
		}
		return $statement;
   
   
   
   
   
   
   
    }


     public function Logger($message)
     {
        $logFile = fopen("c://temp//log.txt", 'a') or die("Error creando archivo");
        fwrite($logFile, "\n".date("d/m/Y H:i:s")."->". $message) ;
        fclose($logFile);
     }   


     


}