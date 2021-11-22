<?php
namespace App\EventHandlers;

use App\Core\EventHandler;
use PHPMailer\PHPMailer\PHPMailer;

class EmailEventHandler implements EventHandler{
    private $toAddresses;
    private $ccAddresses;
    private $bccAddresses;
    private $attachments;
    private $subject;
    private $body;
    private $attemtCount;
    private $emailProvider;
    public function addAddress(string $address){
        $this->toAddresses[]= $address;
    }
    public function addCC(string $address){
        $this->ccAddresses[]= $address;
    }
    public function addBCC(string $address){
        $this->bccAddresses[]= $address;
    }
    public function addAttachment(string $path){
        $this->attachments[]= $path;
    }
    public function setSubject(string $subject){
        $this->subject= $subject;
    }
    public function setBody(string $body){
        $this->body= $body;
    }
    public function __construct($emailProvider)
    {
        $this->emailProvider = $emailProvider;
        $this->toAddresses = [];
        $this->ccAddresses = [];
        $this->bccAddresses = [];
        $this->attachments = [];
        $this->subject = '';
        $this->body = '';
        $this->serverSetings();
        $this->attemtCount = 0;
    }
    public function getData(): string {
        return \json_encode((object)[
            'to'=> $this->toAddresses ,
            'cc'=> $this->ccAddresses ,
            'bcc'=> $this->bccAddresses ,
            'att'=> $this->attachments ,
            'sub'=> $this->subject ,
            'txt'=> $this->body ,
            'count'=> $this->attemtCount
        ]);
    }
    public function setData(string $searilized_data) {
        $data = json_decode($searilized_data);
        if(!$data) return;

        $this->toAddresses = $data->to;
        $this->ccAddresses = $data->cc;
        $this->bccAddresses = $data->bcc;
        $this->attachments = $data->att;
        $this->subject =  $data->sub;
        $this->body =  $data->txt;
        $this->attemtCount = $data->count;
    }
    public function serverSetings(){
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

    public function handle():string {
            
                   //Recipients
                      $this->emailProvider->setFrom('customerservice@mobileshop.com', 'Customer Service');
                      $this->emailProvider->addReplyTo('info@mobileshop.com', 'Information');

                      foreach ($this->toAddresses  as $toAddress) {
                        $this->emailProvider->AddAddress($toAddress);
                      }
                    

                      foreach ($this->ccAddresses as  $ccAddress) {
                        $this->emailProvider->addCC( $ccAddress);
                    }
                    
                  
                      foreach (  $this->bccAddresses  as $bccAddress) {
                        $this->emailProvider->addBCC($bccAddress);
                    }
                    
                    foreach ($this->attachments  as $attachment) {
                        $this->emailProvider->addAttachment($attachment);
                      }
                    
                   // Content
                      $this->emailProvider->isHTML(true);                                  // Set email format to HTML
                      $this->emailProvider->Subject = $this->subject;
                      $this->emailProvider->Body =  $this->body;

                      $this->attemtCount++;
                      $res  = $this->emailProvider->send();
                       // echo 'Message has been sent';
                   if(!$res){
                    if($this->attemtCount > 5) return 'failed';
                    
                    return 'pending';
                   }
                   return 'done';
    }



}