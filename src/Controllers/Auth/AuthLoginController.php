<?php
namespace App\Controllers\Auth;

use App\Core\Controller;
use App\Models\UserModel;
use App\Models\EmailModel;
use App\Controllers\UserController;
use App\Controllers\EmailController;

class AuthLoginController extends Controller{

    public function login($email,$pass ){

          //check if email exist and if email belongs to registered user
          $emailController = new EmailController($this->pdo);
          $emailController->setEmailAddress($email);
          $email_address = $emailController->getEmailAddress();
          $emailModel= new EmailModel($this->pdo);
          $response['status'] = 0; 

         if($emailModel->checkEmailAdressForUser($email_address)){
             //check password 
             $userController = new UserController($this->pdo);
             $userModel = new UserModel($this->pdo);
             $response= $userModel->checkPasswordViaEmailAddress($email_address,$pass);
         }

          
                 return $response;
    }
}