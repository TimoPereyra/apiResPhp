<?php

    // #########################################################
    // #########################################################
    //  BETConnection : 
    //  Method: 
    /*  Whish-Gateway-API will communicate with the client with 3 main endponts: 1) Balance, 2) Debit, 3) 
        Credit

        2) DEBIT:
        Endpoint: POST: https://your_route_wallet/wallet/{walletId}/debit?currency={currency}request: { amount: 
        1.03, reference: “fcfe7495bfb7”, details: {request: {userId: “61cca8be4a4b55c071b11963”,currency: “USD”,},type: 
        “plays”,description: “Apuesta en juego baskeball”,platformName: “GBS”,},

        3) CREDIT
        Endpoint: POST: https://your_route_wallet/wallet/{walletId}/credit?currency={currency}request: {amount: 
        10.00,reference: “fcfe7497”,details: {request: {userId: “61cca8be4a4b55c071b11963”,currency: “USD”,},type: 
        “plays”,description: “Premio en juego baskeball”,platformName: “GBS”,},}

        4) TRANSACTION
        Endpoint: GET: https://your_route_wallet/wallet/{transactionId}/transactionEjemplo de resultado 
        esperado:statusCode: 200response: {_id”: “62ae9f9222215d8812766884”,“walletId”: “628ea654f2141b9c700a8127”,
        “from”: “debit”,“reference”: “62ae9e7ef3bb0a760157e527”,“balance”: {“amount”: 0.09,“currency”: 
        “USD”,“balance_before”: 12000.009999999998,“balance_after”: 11999.919999999998 },“details”: {“request”: 
        {“amount”: “0.09”,“gameId”: vs25kingdomsnojp”,“hash”: “4b672fbb1bc9ccb54855df40c4db5685”,“providerId”: 
        “PragmaticPlay”,“reference”: “62ae9e7ef3bb0a760157e527”,“roundDetails”: “spin”,“roundId”: “2819387934”,“timestamp”: 
        “1655611006485”,“token”:2cfa1909a99ad9e43b9619ed3ca390e4b61436ba87d3f890d4af3823aed335e30ed8488f815becedb
        17575b3a3d93e755734e08269a209287d06a03b47da9f5356da265f9125d9a78c359db4a08c4fd8”,“userId”: 
        “628ea654f2141b9c700a8125”},“type”: “plays”,“description”: “Apuesta en 3 Kingdoms - Battle of Red Cliffs de 
        PragmaticPlay”,“platformName”: “PragmaticPlay”},}
    */
    // #########################################################
    // #########################################################

    include_once "../config.php";
    include_once "../controllers/WalletUserController.php";
    
   
    switch  ($_SERVER['REQUEST_METHOD'])
    {
        case 'GET':         //TRANSACTION

            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $uri = explode( '/', $uri );

            $cont = 0;
            $transactionId = 0;

            foreach ($uri as $item )
            {    
                if ($item == 'wallet')
                {
                    $transactionId = $uri[$cont+1];
                }
                $cont++;
            }

            $response = (new WalletUser)->Transaction($transactionId);
            echo $response;
            break;

        case 'POST':        // DEBIT | CREDIT

            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $uri = explode( '/', $uri );

            $cont = 0;
            $walletId = 0;
            $operation = "";

            foreach ($uri as $item )
            {    
                if ($item == 'wallet')
                {
                    $walletId = $uri[$cont+1];
                    $operation = $uri[$cont+2];
                }
                $cont++;
            }

            $data = json_decode(file_get_contents('php://input'), true);

            $currency = $data['currency'];
            $request = $data['request'];
            
            foreach ($request as $key => $value) 
            {
                if ($key == "amount") $amount = strval($value);
                if ($key == "reference") $reference = $value;
                if ($key == "details")
                {
                    foreach ($value as $key => $detvalue) 
                    {
                        if ($key == "type") $type=$detvalue;
                        if ($key == "description") $description=$detvalue;
                        if ($key == "platformName") $platformName=$detvalue;
                        if ($key == "request") 
                        {
                            foreach ($detvalue as $key => $detrqvalue) 
                            {
                                if ($key == "userId") $user_id=$detrqvalue;
                            }

                        }
                    }

                }
            }
            

            switch ($operation)
            {
                case 'credit':
                    $response = (new WalletUser)->Credit($currency, $amount, $reference, $user_id, $type, $description, $platformName,$walletId);
                    break;
                case 'debit':
                    $response = (new WalletUser)->Debit($currency, $amount, $reference, $user_id, $type, $description, $platformName,$walletId);
                    break;
                default:
                    break;

            }
            $response = json_encode($response);
            echo $response;
            break;
        
        default:
             //En caso de que ninguna de las opciones anteriores se haya ejecutado
            http_response_code(404);
            echo json_encode(
                array("-1","Method not enabled"));
    }


   
  

?>
