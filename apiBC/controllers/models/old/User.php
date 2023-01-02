<?php
require_once '../libraries/Database.php';

class User extends Database
{

    private $pdo;

    public function __construct()
    {
        $this->pdo = parent::connect();
    }

    //Find user by email or username
    public function findUserByEmailOrUsername($email, $username)
    {
        $sql = 'SELECT * FROM tm_usr_user WHERE usr_name = :username OR upe_mail = :email';  //aca se haría el select de un SP que contenga tanto el usr_name cmo upe_mail
        $result = $this->pdo->prepare($sql);
        $result->bindParam(':username', $username, PDO::PARAM_STR);
        $result->bindParam(':email', $email, PDO::PARAM_STR);

        $result->execute();

        $rows = $result->fetch();

        //Check row
        if ($result->rowCount() > 0) {
            return $rows;
        } else {
            return false;
        }
    }

    //Login user
    public function login($nameOrEmail, $password) //es medio extraño este login
    {
        $rows = $this->findUserByEmailOrUsername($nameOrEmail, $nameOrEmail); //deberia de funcionar ya que verifica el username/mail con la funcion de arriba 

        if ($rows == false) {
            return false;
        }

        $hashedPassword = $rows->usr_password;
        if (password_verify($password, $hashedPassword)) {             //y luego verificar la password
            return $rows;
        } else {
            return false;
        }
    }

    //Register User
    public function register($name, $psw, $email)
    {
        $hashedPassword = password_hash($psw, PASSWORD_DEFAULT);

        $sql = 'exec dbo.spRegistrar_User @name=:name,@psw=:psw, @mail=:mail';
        $result = $this->pdo->prepare($sql);

        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':psw', $hashedPassword, PDO::PARAM_STR);
        $result->bindParam(':mail', $email, PDO::PARAM_STR);

        //Execute
        if ($result->execute()) {
            $rows = $result->fetch();
            return $rows;
        } else {
            return false;
        }
    }


    //OLD REGISTER
    // public function Register($name, $psw, $name_person, $last_name, $email, $country, $bdate, $cel, $iden, $moneda)
    // {
    //     try {

    //         $bus = 1;
    //         $rol = 4;
    //         $cou = 'AO';
    //         $balance = 0;
    //         $usr_dv = hash("sha256", $name . $bus, false);
    //         $acc_dv = hash("sha256", $balance, false);
    //         $passw = hash("sha256", $psw, false);
    //         $dd = date('Ymd', strtotime($bdate));


    //         $sql = "exec dbo.spRegistrar_User @name=:name,@psw=:psw,@bus=:bus,@rol=:rol,@name_person=:name_person,@last_name_person=:last_name_person,@mail=:mail,@cou=:cou,@bdate=:bdate,@phone=:phone,@identity=:identity,@cur=:cur,@balance=:balance,@usr_dv=:usr_dv,@acc_dv=:acc_dv";

    //         $result = $this->pdo->prepare($sql);

    //         $result->bindParam(':name', $name, PDO::PARAM_STR);
    //         $result->bindParam(':psw', $passw, PDO::PARAM_STR);
    //         $result->bindParam(':bus', $bus, PDO::PARAM_INT);
    //         $result->bindParam(':rol', $rol, PDO::PARAM_INT);
    //         $result->bindParam(':name_person', $name_person, PDO::PARAM_STR);
    //         $result->bindParam(':last_name_person', $last_name, PDO::PARAM_STR);
    //         $result->bindParam(':mail', $email, PDO::PARAM_STR);
    //         $result->bindParam(':cou', $cou, PDO::PARAM_STR);
    //         $result->bindParam(':bdate', $dd, PDO::PARAM_STR);
    //         $result->bindParam(':phone', $cel, PDO::PARAM_STR);
    //         $result->bindParam(':identity', $iden, PDO::PARAM_STR);
    //         $result->bindParam(':cur', $moneda, PDO::PARAM_STR);
    //         $result->bindParam(':balance', $balance, PDO::PARAM_INT);
    //         $result->bindParam(':usr_dv', $usr_dv, PDO::PARAM_STR);
    //         $result->bindParam(':acc_dv', $acc_dv, PDO::PARAM_STR);

    //         $result->execute();
    //         $rows = $result->fetch();
    //         return $rows;
    //     } catch (PDOException $e) {
    //         exit("¡Error!: " . $e->getMessage());
    //     }
    // }

    //Reset Password
    public function resetPassword($newPwdHash, $tokenEmail)                       
    {
        $sql = 'UPDATE tm_usr_user SET usr_password=:psw WHERE upe_mail=:mail';  //aca se haría un update de un SP que contenga tanto el usr_password cmo upe_mail
        $result = $this->pdo->prepare($sql);
        $result->bindParam(':psw', $newPwdHash);
        $result->bindParam(':mail', $tokenEmail);

        //Execute
        if ($result->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
