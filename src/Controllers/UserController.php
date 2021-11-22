<?php

namespace App\Controllers;

use App\Core\Config;
use App\Core\Controller;
use App\Models\UserModel;
use App\Core\Session\SessionInstance;

class UserController extends Controller
{
    private $firstname;
    private $lastname;
    private $email;
    private $password;
    
    public function register(){

    }

    public function login(){
        
    }

    public function getUser(){
        return [
            "password"  => $this->getPassword(),
            "user_status_id" => Config::USER_STATUS_REGISTER,
            "user_role_id" => Config::USER_ROLE_USER,
            "customer_id"=> $this->customer_id,
        ];
    }

   

    /**
     * Get the value of password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */
    public function setPassword($password)
    {
        $this->password = password_hash($password , PASSWORD_DEFAULT);
    }
    public function setCustomerId($customer_id)
    {
        $this->customer_id = $customer_id;
    }

    public function checkPassword($password)
    {
        $response['status'] = null;
        if (strlen($password) < 8) {
            $response['status'] = Config::PASSWORD_MINIMUM_LENGTH;
        } else if (!preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
            $response['status'] = Config::PASSWORD_MINIMUM_REQUIREMENTS;
        }
        return $response;
    }

    public function getPersonalInformation(){
        $id=$this->getSession()->get("user_id");
        $userModel = new UserModel($this->pdo);
        $user= $userModel->getPersonalInformationForUser($id);
        echo json_encode($user);
    }
    public function getAllToDasboard(){
        $userModel = new UserModel($this->pdo);
        $users= $userModel->getAllPersonalInformationToAdmin();
        echo json_encode($users);
    }

    public function getToDasboard($id){
        $userModel = new UserModel($this->pdo);
        $user= $userModel->getAllPersonalInformationToAdmin(true , $id);
        echo json_encode($user);
    }

}
