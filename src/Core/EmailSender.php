<?php
namespace App\Core;

use PHPMailer\PHPMailer\PHPMailer;

class EmailSender{
    public $emailProvider;
    
    public function __construct($emailProvider){
        $this->emailProvider = $emailProvider;
        $this->serverSetings();
    }
    public function serverSetings(){
         //Server settings
                          // Enable verbose debug output     $this->emailProvider->SMTP::DEBUG_SERVER; 
     //Server settings  
                         // Enable verbose debug output     $this->emailProvider->SMTP::DEBUG_SERVER; 
                         $this->emailProvider->isSMTP();                                            // Send using SMTP
                         $this->emailProvider->Host       = $_ENV['SMTP_HOST'];                  // Set the SMTP server to send through
                         $this->emailProvider->SMTPAuth   = true;                                   // Enable SMTP authentication
                         $this->emailProvider->Username   = $_ENV['SMTP_USERNAME'];                   // SMTP username
                         $this->emailProvider->Password   = $_ENV['SMTP_PASSWORD'];                             // SMTP password
                         $this->emailProvider->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
                         $this->emailProvider->Port       = $_ENV['SMTP_PORT'];                                  
    }
    public  function send($firstname,$email_address,$activation_code){
        $html = "<!doctype html><html><meta charset='utf-8'></head><body>";
        $html .= "<h1>Welcome to mobile shop " . $firstname . "</h1><br/>" . PHP_EOL;
        $html .= "<p>Please, to activate your profile click on  <a href='http://localhost:3000/verify/" . $email_address . "/" . $activation_code . "'>LINK</a></p>";
        $html .= "</body></html>";
     
            //Recipients
               $this->emailProvider->setFrom('customerservice@mobileshop.com', 'Customer Service');
               $this->emailProvider->addAddress($email_address, $firstname);     // Add a recipient
               $this->emailProvider->addReplyTo('info@mobileshop.com', 'Information');
               $this->emailProvider->addCC($email_address);
    
            // Content
               $this->emailProvider->isHTML(true);                                  // Set email format to HTML
               $this->emailProvider->Subject = 'CONFIRM YOUR REGISTRATION';
               $this->emailProvider->Body    =  $html;
    
           
                // echo 'Message has been sent';
                file_put_contents("email_msg.php", $html);
                return \boolval(   $this->emailProvider->send());
           
    }
}