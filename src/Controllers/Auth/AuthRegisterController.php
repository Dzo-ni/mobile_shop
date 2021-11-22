<?php
namespace App\Controllers\Auth;

use App\Core\Config;
use App\Core\Controller;
use App\Core\EmailSender;
use App\Models\UserModel;
use App\Models\EmailModel;
use App\Models\CustomerModel;
use App\Response\ResponseMessage;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\Controllers\UserController;
use App\Controllers\EmailController;
use App\Controllers\CustomerController;
use App\Controllers\Customer\StripeCustomer;
use App\Core\RandomString;
use App\EventHandlers\EmailEventHandler;
use App\Models\EventModel;

class AuthRegisterController extends Controller{
    use RandomString;
    public function register($firstname , $lastname, $pass, $repass, $email_address){
         //emailController
        $emailController = new EmailController($this->pdo);
        if (!$emailController->checkEmail($email_address)) {
           $response= ResponseMessage::emailInvalid();
           echo json_encode($response);
            exit;
        }
        if($emailController->checkIfEmailExist($email_address)) {
            $response = ResponseMessage::emailExistInDatabase();
            echo json_encode($response);
            exit;
        }
        $userController = new UserController($this->pdo);
        $response = $userController->checkPassword($pass);
        if ($response['status'] == Config::PASSWORD_MINIMUM_LENGTH) {
            $response = ResponseMessage::passwordMinimumLength();
            echo json_encode($response);
            exit;
        }elseif ($response['status'] == Config::PASSWORD_MINIMUM_REQUIREMENTS) {
            $response = ResponseMessage::passwordMinimumRequirements();
            echo json_encode($response);
            exit;
        }

        try{
        $this->pdo->beginTransaction();
        define("IKEY", uniqid("",true));
        //ADD CUSTOMER
        $customerController= new CustomerController($this->pdo);
        $customerController->setFirstname($firstname);
        $customerController->setLastname($lastname);
        $stripeCustomer=new StripeCustomer($this->pdo);
        $stripe_customer_id= $stripeCustomer->create(IKEY, $email_address);
        $customerController->setCustomerStripeId($stripe_customer_id);
        $customer=$customerController->getCustomer();
       
        $customerModel = new CustomerModel($this->pdo);
        $customer_id= $customerModel->add($customer);
     

        //ADD USER
        $userController->setPassword($pass);
        $userController->setCustomerId($customer_id);
        $user = $userController->getUser();
        // $userController->setEmail($email);
       $userModel = new UserModel($this->pdo);
       $userModel->add($user);
        //  //emailController

        //ADD EMAIL
     
        $userController = new EmailController($this->pdo);
        $emailController->setEmailAddress($email_address);
        $emailController->setCustomerId($customer_id);
        $emailController->setActivationCode($this->generateRandomString()); 
        $email= $emailController->getEmail();
        $emailModel = new EmailModel($this->pdo);
        $emailModel->add( $email);
        $this->pdo->commit();

      
        //$mail = new PHPMailer(true);
          // $emailSender = new EmailSender($mail);
    //    if($emailSender->send($customerController->getFirstname(), $emailController->getEmailAddress(), $emailController->getActivationCode())){
    //      $response = ResponseMessage::registeredSuccess();
    //     }
        $mail = new PHPMailer(true);
        $event = new EmailEventHandler($mail);
        $html = "<!doctype html><html><meta charset='utf-8'></head><body>";
        $html .= "<h1>Welcome to mobile shop " . $customerController->getFirstname() . "</h1><br/>" . PHP_EOL;
        $html .= "<p>Please, to activate your profile click on  <a href='http://localhost:3000/verify/" . $emailController->getEmailAddress() . "/" . $emailController->getActivationCode() . "'>Link</a></p>";
        $html .= "</body></html>";
        $event->setSubject("Confirm your email address");
        $event->setBody($html);
        $event->addAddress($email_address);

        $eventModel= new EventModel($this->pdo);
        $event_id =$eventModel->add(
            [
                "type"=>"email",
                "data"=> $event->getData(),
                "status"=>"pending"
            ]
        );
        $response['status']=0;
        if($event_id){
            $response['event_id']= $event_id;
            $response['status']=1;
        }
        return $response;
       

    }catch(\PDOException $e){
        $this->pdo->rollback();
    }
    
    }
}