<?php

    // #########################################################
    // #########################################################
    //  BETConnection : 
    //  Method: 
    /*  Whish-Gateway-API will communicate with the client with 3 main endponts: 1) Balance, 2) Debit, 3) 
        Credit

        1) AUTHENTICATE AND USER BALANCE
        Endpoint: GET: https://your_route_wallet/wallets?userId={userId}&currency={currency}
        Ejemplo de resultado esperado: statusCode: 200 response: { userId: “61cca8be4a4b55c071b11963”, walletId: 
        “72cda9be5a5e66d182c22074”, currency: “USD”, balance: 235.59, data: { user: { name: “Maria”, lastname: “Perez”, 
        username: “mariaperez”, email: “mariaperez@email.com”, status: true,

    */
    // #########################################################
    // #########################################################

    include_once "config.php";
    include_once "controllers/WalletUserController.php";
   

    switch  ($_SERVER['REQUEST_METHOD'])
    {
        case 'GET':         //AUTHENTICATE AND USER BALANCE 
    
            if (!isset($_GET['userid']) || !isset($_GET['currency']))
            {
                http_response_code(500);
                echo json_encode(
                array('500',"Parameters Not Found"));
                break;

            }

            $userId = $_GET['userid'];
            $currency = $_GET['currency'];

            $response = (new WalletUser)->AuthenticateBalance($userId, $currency );
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
