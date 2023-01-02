<?php

    require_once '../models/User.php';
    require_once '../helpers/session_helper.php';

    class Users {

        private $userModel;
        
        public function __construct(){
            $this->userModel = new User;
        }
        
        //REGISTRO
        public function register(){
            //Process form
            
            //Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            
            //opcion 1, la que ya habiamos usado
            // $model = new User();

            // $rows = $model->register($_POST["name"], $_POST["psw"], $_POST["email"]);

            // if($rows>0){
            //     echo '<script>alert("New account created.")</script>';
            // //redirect to another page
            //     echo '<script>window.location.replace("index.php")</script>';
            // }else{
            //     echo '<script>alert("An error occurred")</script>';
            // }

    
            //opción 2, con muchísimas validaciones, bien completo
            $username = trim($_POST['name']);   
            $email = trim($_POST['email']);
            $psw = trim($_POST['psw']);
            $pswRepeat = trim($_POST['pswRepeat']);
        

            //Validate inputs
            if(empty($username) || empty($email)  || empty($psw) || empty($pswRepeat)){
                flash("register", "Por favor completa todos los campos");
                redirect("../signup.php");
            }

            if(!preg_match("/^[a-zA-Z0-9]*$/", $username)){
                flash("register", "Usuario inválido");
                redirect("../signup.php");
            }

            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                flash("register", "Correo electrónico inválido");
                redirect("../signup.php");
            }

            if(strlen($psw) < 6){
                flash("register", "Contraseña inválida");
                redirect("../signup.php");
            } else if($psw !== $pswRepeat){
                flash("register", "Las contraseñas no coinciden");
                redirect("../signup.php");
            }

            //User with the same email or password already exists
            if($this->userModel->findUserByEmailOrUsername($email, $username)){
                flash("register", "Username or email already taken");
                redirect("../signup.php");
            }

            //Passed all validation checks.
            //Now going to hash password
            $psw = password_hash($psw, PASSWORD_DEFAULT);

            //Register User
            if($this->userModel->register($username, $email, $psw)){
                redirect("../login.php");
            }else{
                die("Error");
            }
        }

    public function login(){
        //Sanitize POST data
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        
            $nameOrEmail = trim($_POST['nameOrEmail']);
            $psw = trim($_POST['psw']);
        

        if(empty($nameOrEmail) || empty($psw)){
            flash("login", "Por favor completa todos los campos");
            header("location: ../login.php");
            exit();
        }

        //Check for userOrEmail
        if($this->userModel->findUserByEmailOrUsername($nameOrEmail, $nameOrEmail)){
            //User Found
            $loggedInUser = $this->userModel->login($nameOrEmail, $psw);
            if($loggedInUser){
                //Create session
                $this->createUserSession($loggedInUser);
            }else{
                flash("login", "Contraseña incorrecta");
                redirect("../login.php");
            }
        }else{
            flash("login", "Ningún usuario encontrado");
            redirect("../login.php");
        }
    }

    public function createUserSession($user){
        $_SESSION['usr_id'] = $user->usr_id;
        $_SESSION['usr_name'] = $user->usr_name;
        $_SESSION['upe_mail'] = $user->upe_mail;
        redirect("../index.php");
    }

    public function logout(){
        unset($_SESSION['usr_id']);
        unset($_SESSION['usr_name']);
        unset($_SESSION['upe_mail']);
        session_destroy();
        redirect("../index.php");
    }
}

    $init = new Users;

    //Ensure that user is sending a post request
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        switch($_POST['type']){
            case 'register':
                $init->register();
                break;
            case 'login':
                $init->login();
                break;
            default:
            redirect("../index.php");
        }
        
    }else{
        switch($_GET['q']){
            case 'logout':
                $init->logout();
                break;
            default:
            redirect("../index.php");
        }
    }

    