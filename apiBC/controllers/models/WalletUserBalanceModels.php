<?php

class mWalletUserBalance extends Database
{
    private $pdo;
    private $pdoTrx;
    public function __construct()
    {
        $this->pdo = parent::connect();
        $this->pdoTrx = parent::connectTrx();
    }

    public function Get($userid, $currency)
    {
        try 
        {
            $sql = 'exec dbo.spgUserToken @usr_id=:usrid';
            $result = $this->pdo->prepare($sql);
            $result->bindParam(':usrid', $userid, PDO::PARAM_STR);
            $result->execute();
            $tok = $result->fetch(PDO::FETCH_OBJ);

            $sql = 'exec dbo.spAuthenticate_API @token=:token';
            $result = $this->pdo->prepare($sql);
            $result->bindParam(':token', $tok->ust_token, PDO::PARAM_STR);
            $result->execute();
            $rows = $result->fetch(PDO::FETCH_OBJ);
            $response = new eResponse;
            $userbalance = new eauthenticationbalance;
            $userdata = new euser;    

            //$userbalance->userId = hash("MD5",$userid, false);
            $userbalance->userId = $userid;
            $userbalance->currency = $currency;

            $userdata->email = $rows->email;
            $userdata->name = $rows->firstName;
            $userdata->lastname = $rows->lastName; 
            $userdata->username = $rows->userName;
            
           
            $vacio = NULL;
            $sql = 'exec dbo.spgAccountBalance @acc_id=:acc_id, @usr_id=:usr_id';
            $result = $this->pdoTrx->prepare($sql);
            $result->bindParam(':acc_id', $rows->acc_id, PDO::PARAM_STR);
            $result->bindParam(':usr_id', $vacio, PDO::PARAM_STR);
            $result->execute();
            $balance = $result->fetch(PDO::FETCH_OBJ);
            
            //$userbalance->walletId = hash("MD5", $rows->acc_id, false);
            $userbalance->walletId =  $rows->acc_id;

            if (!isset($balance->acc_balance))
                $userbalance->balance = 0.00;
            else
                $userbalance->balance = $balance->acc_balance;


            $userbalance->data = array($userdata);

            $response->statusCode = 200;
            $response->response = array($userbalance);

            return $response;
        }
        catch (Exception $e)
        {
            $response_error = (new eResponse);
            $response_error->statusCode = 500;
            $ee = (new eError);
            $response_error->response = array($ee);

            return $response_error;
        }
    }

    public function Debit($currency, $amount, $reference, $userId, $type, $description, $platformName,$walletId)
    {
       

       try{
            $response = new eResponse;
            $userbalance = new eauthenticationbalance;
            $userdata = new euser; 
            $ee = (new eError);
            $errorSfound = (new eErrorSfondos);

            $sql = 'exec dbo.spiDebit_BC';
            $result = $this->pdoTrx->prepare($sql);
            $result->execute();
            $result = $result->fetch(PDO::FETCH_OBJ);

            $iditem =$result->deb_id;
            
            $sql = 'exec dbo.spiDebitItem_BC @deb_id=:idItem, @CurrencyId=:currencyId, @Amount=:Amount,@ClientId=:ClientId, @Reference=:Reference, @Description=:Description';
            $result = $this->pdoTrx->prepare($sql);
            $result->bindParam(':idItem', $iditem, PDO::PARAM_STR);
            $result->bindParam(':currencyId', $currency, PDO::PARAM_STR);
            $result->bindParam(':Amount', $amount, PDO::PARAM_STR);
            $result->bindParam(':ClientId', $userId, PDO::PARAM_STR);
            $result->bindParam(':Reference', $reference, PDO::PARAM_STR);
            $result->bindParam(':Description', $description, PDO::PARAM_STR);
            $result->execute();
           


            $vacio = $walletId;
            $sql = 'exec dbo.spgAccountBalance @acc_id=:acc_id, @usr_id=:usr_id';
            $result = $this->pdoTrx->prepare($sql);
            $result->bindParam(':acc_id', $vacio, PDO::PARAM_STR);
            $result->bindParam(':usr_id', $userId, PDO::PARAM_STR);
            $result->execute();
            $balance = $result->fetch(PDO::FETCH_OBJ);

            $balanceCount = $balance->acc_balance;
            if ( $balanceCount <0){
                $response->statusCode = 401;
                $response->response = array($errorSfound);
            }
            else
            {
                $response->statusCode = 200;
                $response->response = array($userbalance);
                $sql = 'exec dbo.spgUserToken @usr_id=:usrid';
                $result = $this->pdo->prepare($sql);
                $result->bindParam(':usrid', $userId, PDO::PARAM_STR);
                $result->execute();
                $tok = $result->fetch(PDO::FETCH_OBJ);
                
                $sql = 'exec dbo.spAuthenticate_API @token=:token';
                $result = $this->pdo->prepare($sql);
                $result->bindParam(':token', $tok->ust_token, PDO::PARAM_STR);
                $result->execute();
                $rows = $result->fetch(PDO::FETCH_OBJ);

            
                $userbalance->userId = $userId;
                $userbalance->walletId = $walletId;
                $userbalance->currency = $rows->currencyId;
                $userbalance->balance = $balanceCount;
                $userbalance->data = $description;

               

            }
            
            return $response;


       }
       catch(Exception $e){
            $response_error = (new eResponse);
            $response_error->statusCode = 500;
            $ee = (new eError);
            $response_error->response = array($ee);

            return $response_error;
       }
    }

    public function Credit ($currency, $amount, $reference, $userId, $type, $description, $platformName,$walletId)
    {
      try {
            
       
        $response = new eResponse;
        $userbalance = new eauthenticationbalance;
        $userdata = new euser; 
        $ee = (new eError);
        $errorSfound = (new eErrorSfondos);

        $sql = 'exec dbo.spiCredit_BC';
        $result = $this->pdoTrx->prepare($sql);
        $result->execute();
        $result = $result->fetch(PDO::FETCH_OBJ);
        $iditem =$result->cre_id;
        $sql = 'exec dbo.spiCreditItem_BC @cre_id=:idItem, @CurrencyId=:currencyId, @Amount=:Amount,@ClientId=:ClientId, @Reference=:Reference, @Description=:Description';
        $result = $this->pdoTrx->prepare($sql);
        $result->bindParam(':idItem', $iditem, PDO::PARAM_STR);
        $result->bindParam(':currencyId', $currency, PDO::PARAM_STR);
        $result->bindParam(':Amount', $amount, PDO::PARAM_STR);
        $result->bindParam(':ClientId', $userId, PDO::PARAM_STR);
        $result->bindParam(':Reference', $reference, PDO::PARAM_STR);
        $result->bindParam(':Description', $description, PDO::PARAM_STR);
        $result->execute();
       
        $vacio = $walletId;
        $sql = 'exec dbo.spgAccountBalance  @usr_id=:usr_id, @acc_id=:acc_id';
        $result = $this->pdoTrx->prepare($sql);
        $result->bindParam(':usr_id', $userId, PDO::PARAM_STR);
        $result->bindParam(':acc_id', $vacio, PDO::PARAM_STR);
        $result->execute();
        $balance = $result->fetch(PDO::FETCH_OBJ); 
       
        $balanceCount = $balance->acc_balance;
      
        
        
        if ( $balanceCount <0)
        {
            $response->statusCode = 401;
            $response->response = array($errorSfound);
        }
        else
        {
            $response->statusCode = 200;
            $response->response = array($userbalance);
            $response->statusCode = 200;
            $sql = 'exec dbo.spgUserToken @usr_id=:usrid';
            $result = $this->pdo->prepare($sql);
            $result->bindParam(':usrid', $userId, PDO::PARAM_STR);
            $result->execute();
            $tok = $result->fetch(PDO::FETCH_OBJ);
            
            $sql = 'exec dbo.spAuthenticate_API @token=:token';
            $result = $this->pdo->prepare($sql);
            $result->bindParam(':token', $tok->ust_token, PDO::PARAM_STR);
            $result->execute();
            $rows = $result->fetch(PDO::FETCH_OBJ);

           
            $userbalance->userId = $userId;
            $userbalance->walletId = $walletId;
            $userbalance->currency = $rows->currencyId;
            $userbalance->balance = $balanceCount;
            $userbalance->data = $description; 
        }

        return $response;

      }
      catch(Exception $e)
      {
        $response_error = (new eResponse);
        $response_error->statusCode = 500;
        $ee = (new eError);
        $response_error->response = array($ee);

        return $response_error;
      }

    }

    public function Transaction($transactionId)
    {
        $transac  = new etransaction;
        $tbalance = new ebalance;
        $tedetails = new edetails;
        $terequest = new erequest;
        $response = new eResponse;

        //cargar $etransaction

        //cargar $tbalance
        $transac->balance = $tbalance;    

        
        //cargar $tdetails

        // $details->request = $terequest;
        // $details->type = "";
        // $details->description = "";
        // $details->platformName ="";

        // $transac->details = $tdetails;   
        
        
        // $response->$transac;


        

        return $transactionId;

    }
    

}

class eError
{

    public $mensaje="Error General";
}
class eErrorSfondos
{

    public $mensaje="Error: user has no funds”";
}
class eResponse
{
    public $statusCode=0;
    public $response;
}

class eauthenticationbalance
{
    public $userId;
    public $walletId;
    public $currency;
    public $balance;
    public $data;
}

class euser
{
    public $name;
    public $lastname;   //1 Masculino 0 Femenino
    public $username;
    public $email="";
    public $status=true;
}

class etransaction
{
    public $_id;
    public $walletId;
    public $from;       //debit | credit
    public $reference;
    public $balance;
    public $details;
}

class ebalance
{
    public $amount;
    public $currency;
    public $balance_before;
    public $balance_after;

}

class edetails
{
    public $request;
    public $type;
    public $description;
    public $platformName;

}

class erequest
{
    public $amount;
    public $gameId;
    public $hash;
    public $providerId;
    public $reference;
    public $roundDetails;
    public $roundId;
    public $timestamp;
    public $token;
    public $userId;
}

