<?php

    include_once "BaseController.php";
    include_once "libraries/Database.php";
    include_once "models/WalletUserBalanceModels.php";

   class WalletUser extends BaseController {

        public function AuthenticateBalance( $userid, $currency)
        {
            return  (new mWalletUserBalance)->Get($userid, $currency);
            //return "Waller Controller - AuthenticateBalance";

        }

        public function Debit($walletid, $currency, $amount, $reference, $userId, $type, $description, $platformName)
        {
            return  (new mWalletUserBalance)->Debit($walletid, $currency, $amount, $reference, $userId, $type, $description, $platformName);

        }


        public function Credit($walletid, $currency, $amount, $reference, $userId, $type, $description, $platformName)
        {
            return  (new mWalletUserBalance)->Credit($walletid, $currency, $amount, $reference, $userId, $type, $description, $platformName);

        }


        public function Transaction($transactionId)
        {
            return  (new mWalletUserBalance)->Transaction($transactionId);

        }




    }
?>
    