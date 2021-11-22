<?php
namespace App\Controllers;

use App\Core\Config;
use App\Core\Controller;
use App\Core\UserInformationTrait;
use App\Models\EmailModel;

class EmailController extends Controller{
    use UserInformationTrait;
    private $activation_code;
    public function getEmail(){
        return [
            "email_address"=>$this->getEmailAddress(),
            "activation_code"=>$this->getActivationCode(),
            "email_status_id"=>Config::EMAIL_NOT_VERIFIED,
            "on_mailing"=> 1,
            "is_customer_registered"=>1,
            "customer_id"=>$this->customer_id,
        ];
    }

    public function checkEmail($email)
    {
        $res = true;
        if (!preg_match('/^[a-z][a-zA-Z_\d]+\@[a-z]+\.[a-z]{2,4}$/', $email)) {
            $res = false;
        }
        return $res;
    }

    public function checkIfEmailExist($email){
        $emailModel = new EmailModel($this->pdo);
       return $emailModel->checkUserEmail($email) === false;
    }
/**
     * Get the value of email
     */
    public function getEmailAddress()
    {
        return $this->email_address;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */
    public function setEmailAddress($email_address)
    {

        $this->email_address = $email_address;
    }

    public function setCustomerId($customer_id)
    {
        $this->customer_id = $customer_id;
    }


    /**
     * Get the value of activation_code
     */ 
    public function getActivationCode()
    {
        return $this->activation_code;
    }

    /**
     * Set the value of activation_code
     *
     * @return  self
     */ 
    public function setActivationCode($activation_code)
    {
        $this->activation_code = $activation_code;

        return $this;
    }

    public function verified($email, $activation_code)
    {
     
        $emailModel= new EmailModel($this->pdo);
       $response['status'] = 0;
        $isVerified = $emailModel->verify($email, $activation_code);
        if( $isVerified){
            $user_id= $emailModel->getUserId($email);
            $user=$this->setUserInformationInSession($user_id);
            $response['status'] =1 ;
            $response['user']= $user;
        }
       die(json_encode($response));
    }
}





 
