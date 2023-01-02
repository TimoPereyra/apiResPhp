<?php

    require_once 'BaseController.php';

   class CheckTxStatus extends BaseController {

        public function get()
        {

            // if (isset($_GET['id']))
            // {
            // //Mostrar un post
            // $sql = '';
            // //$sql = $dbConn->prepare("SELECT * FROM posts where id=:id");
            // $sql->bindValue(':id', $_GET['id']);
            // $sql->execute();
            // header("HTTP/1.1 200 OK");
            // echo json_encode(  $sql->fetch(PDO::FETCH_ASSOC)  );
            // exit();
            // }
            // else {
            // //Mostrar lista de post
            // //$sql = $dbConn->prepare("SELECT * FROM posts");
            // $sql->execute();
            // $sql->setFetchMode(PDO::FETCH_ASSOC);
            // header("HTTP/1.1 200 OK");
            // echo json_encode( $sql->fetchAll()  );
            // exit();
            // }

            // $sql = 'exec dbo.spRegistrar_User @name=:name,@psw=:psw, @mail=:mail';
            // $result = $this->pdo->prepare($sql);
    
            // $result->bindParam(':name', $name, PDO::PARAM_STR);
            // $result->bindParam(':psw', $hashedPassword, PDO::PARAM_STR);
            // $result->bindParam(':mail', $email, PDO::PARAM_STR);
    
            // //Execute
            // if ($result->execute()) {
            //     $rows = $result->fetch();
            //     return $rows;
            // } else {
            //     return false;
            // }
            return true;
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
    