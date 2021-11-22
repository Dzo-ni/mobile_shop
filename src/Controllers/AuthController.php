<?php

namespace App\Controllers;

use App\Core\Config;
use App\Core\Controller;
use App\Core\EmailSender;
use App\Models\UserModel;
use App\Models\EmailModel;
use App\Core\Session\Session;
use App\Models\CustomerModel;
use App\Models\StripeUserModel;
use App\Response\ResponseMessage;
use App\Controllers\EmailController;
use App\Core\Session\SessionInstance;
use App\Controllers\Customer\StripeCustomer;
use App\Controllers\Auth\AuthLoginController;
use App\Controllers\Auth\AuthRegisterController;
use App\Core\UserInformationTrait;

class AuthController extends Controller{
    use UserInformationTrait;
    public function userLogin(){
        $response['status']= 0 ;
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        $user = $data->user;

        $email = trim(filter_var($user->email, FILTER_SANITIZE_STRING));
        $pass = trim(filter_var($user->password, FILTER_SANITIZE_STRING));

        if ($email == "" || $pass == "") {
            $response['msg']= "All fields must be filled" ;

            echo json_encode($response);
            die;
        }

        $authLoginController = new AuthLoginController($this->pdo);
        
        $response  = $authLoginController->login($email, $pass); 
        if($response['status']){
            $user=$this->setUserInformationInSession($response['user_id']);
            $userModel = new UserModel($this->pdo);
            $response['user']=$user;
            $response["isAdmin"] = $userModel->getByIdAndRole($response['user_id'], Config::USER_ROLE_ADMINISTRATOR ) ; 

        }else{
            $response['msg']= "User doesn't exist";
        }
       
      
        echo json_encode($response);
    }

    public function userRegister(){
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        $user = $data->user;

        $firstname = trim(filter_var($user->firstname, FILTER_SANITIZE_STRING));
        $lastname = trim(filter_var($user->lastname, FILTER_SANITIZE_STRING));
        $pass = trim(filter_var($user->password, FILTER_SANITIZE_STRING));
        $repass = trim(filter_var($user->repassword, FILTER_SANITIZE_STRING));
        $email_address = trim(filter_var($user->email, FILTER_SANITIZE_STRING));
        
        if ($pass != $repass) {
            $response = ResponseMessage::passwordsDoesntMatch();
            echo json_encode($response);
            exit();
        }
        
        $authRegisterController = new AuthRegisterController($this->pdo);
        $response = $authRegisterController->register($firstname , $lastname, $pass, $repass, $email_address);
        if($response["status"]){
           $event_id= $response['event_id'];
            $authLoginController = new AuthLoginController($this->pdo);
            $response  = $authLoginController->login($email_address, $pass); 
            if($response["status"]){
                $user=$this->setUserInformationInSession($response['user_id']);
                $userModel = new UserModel($this->pdo);
                $response['user']=$user;
                $response["isAdmin"] = $userModel->getByIdAndRole($response['user_id'], Config::USER_ROLE_ADMINISTRATOR ) > 2; 
                $response['event_id']= $event_id;
                }
        }
        
        echo json_encode($response);
    }
        public function isLogin(){
            
            // $sessionInstance = new SessionInstance();
            // $session = $sessionInstance->getSessionInstance();
         
            // $this->setSession($session);
            // $this->getSession()->reload();
           
            $response['status']= false;
            if($this->getSession()->has("user_id")){
                $user_id = $this->getSession()->get("user_id");
                $userModel = new UserModel($this->pdo);
                $response["user"] =  $userModel->getPersonalInformationForUser($user_id);
                $response['isAdmin'] = $userModel->getByIdAndRole( $user_id , Config::USER_ROLE_ADMINISTRATOR) ;
              
                $response['status']= true;
            }
           echo json_encode($response);
        }
        
    }
