<?php

class mAutentication extends Database
{
    private $pdo;
    private $pdoTrx;
    public function __construct()
    {
        $this->pdo = parent::connect();
        $this->pdoTrx = parent::connectTrx();
    }

    public function Autenticar($operatorId, $token)
    {

        try 
        {
        $data = new eauthentication;

        $sql = 'exec dbo.spAuthenticate_API @operatorId=:operatorId, @token=:token';
        $result = $this->pdo->prepare($sql);

        $result->bindParam(':operatorId', $operatorId, PDO::PARAM_STR);
        $result->bindParam(':token', $token, PDO::PARAM_STR);

        $result->execute();

        $rows = $result->fetch(PDO::FETCH_OBJ);

       
        $data->playerId = $rows->playerId;
        $data->userName = $rows->userName;
        $data->currencyId = $rows->currencyId;
        $data->birthDate= $rows->birthDate;
        $data->firstName= $rows->firstName;
        $data->lastName= $rows->lastName;
        $data->gender= $rows->gender;
        $data->email= $rows->email;
        $data->isReal= $rows->isReal;

        $sql = 'exec dbo.spgAccountBalance @acc_id=:acc_id';
        $result = $this->pdoTrx->prepare($sql);

        $result->bindParam(':acc_id', $rows->acc_id, PDO::PARAM_STR);
        $result->execute();
        $balance = $result->fetch(PDO::FETCH_OBJ);

        $data->balance = $balance->acc_balance;

            return $data;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }
}

class eauthentication
{
    public $timestamp;
    public $signature;
    public $errorCode;
    public $playerId;
    public $userName;
    public $currencyId;
    public $balance;
    public $birthDate;
    public $firstName;
    public $lastName;
    public $gender;
    public $email;
    public $isReal;


}
