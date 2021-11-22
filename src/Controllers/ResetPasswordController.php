<?php
namespace App\Controllers;
use App\Core\Config;
use App\Core\Controller;
use App\Models\UserModel;
use App\Models\EmailModel;
use App\Core\UserInformationTrait;
use App\Models\ResetPasswordModel;
use App\Controllers\Auth\AuthLoginController;

class ResetPasswordController extends Controller{
    use UserInformationTrait;

    public function handle(){
        $data = file_get_contents("php://input");
        $data = json_decode($data);
        $token= $data->token;
        $email_address = $data->email_address;
        $password=$data->password;
        $response['status'] = 0;
       $emailModel = new EmailModel($this->pdo);
        $email_id = $emailModel->checkEmailAdressForUser($email_address);
       
        if($email_id){
          
            //upisati reset token u reset_password table
            //generete token 
          
            $resetPasswordModel = new ResetPasswordModel($this->pdo);
           
           if($resetPassword= $resetPasswordModel->getByEmailIdAndActiveRow($email_id)){
           
               if($resetPassword->token == $token){
            
                $resetPasswordModel->editById($resetPassword->reset_password_id , [
                    "is_active"=>0
                ]);
                //update password
                $userController = new UserController($this->pdo);
                $userController->setPassword($password);
                $password_hash= $userController->getPassword();
                $userModel = new UserModel($this->pdo);
                $user_id = $emailModel->getUserId($email_address);
                $userModel->editById( $user_id, [
                    "password"=> $password_hash
                ]);
               
              
              
           //otvoriti sesiju
           $authLoginController = new AuthLoginController($this->pdo);
           
           $response  = $authLoginController->login($email_address, $password); 
           if($response['status']){
               $user=$this->setUserInformationInSession($response['user_id']);
               $userModel = new UserModel($this->pdo);
               $response['user']=$user;
               $response["isAdmin"] = $userModel->getByIdAndRole($response['user_id'], Config::USER_ROLE_ADMINISTRATOR ) > 2; 
   
           }
        }
        }

          echo json_encode($response);

      }
    }
}