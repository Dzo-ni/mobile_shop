<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\RandomString;
use App\Models\EmailModel;
use App\Models\EventModel;
use PHPMailer\PHPMailer\PHPMailer;
use App\EventHandlers\EmailEventHandler;
use App\Models\ResetPasswordModel;

class ForgotPasswordController extends Controller{
    use RandomString;
    public function handle($email_address){
        $emailModel = new EmailModel($this->pdo);
        $response['status'] = $emailModel->checkEmailAdressForUser($email_address);
        if($response['status']){
            //upisati reset token u reset_password table
            //generete token 
            $token = $this->generateRandomString(32);
            $resetPasswordModel = new ResetPasswordModel($this->pdo);
            $resetPasswordModel->add(
                [
                    "email_id"=> $response['status'],
                    "token" => $token,
                    "is_active"=> 0
                ]
            );
            //upisi event u bazu i vrati id koji posaljes klijentu koji ce pozvati endpoint za slanje  tog maila
            $mail = new PHPMailer(true);
            $event = new EmailEventHandler($mail);
            
            $html = "<!doctype html><html><meta charset='utf-8'></head><body>";
            $html .= "<h1>Email with reset password link </h1><br/>" ;

            $html .= "<p>Please, to reset your password click on <a href='http://localhost:3000/reset-password/$email_address/$token'>";

            $html .= "link</a></p></body></html>";
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
        }
       echo json_encode( $response ) ;
    }
}