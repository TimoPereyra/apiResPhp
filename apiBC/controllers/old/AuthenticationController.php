<?php

    include_once "BaseController.php";
    include_once "libraries/Database.php";
    include_once "models/AutenticationModels.php";

   class Authentication extends BaseController {

        public function get($operatorId, $token)
        {
            return (new mAutentication)->Autenticar($operatorId, $token);
        }

        public function post()
        {
            return false;
        }

        public function put()
        {
            return false;
        }

        public function delete()
        {
            return false;
        }


    }
?>
    