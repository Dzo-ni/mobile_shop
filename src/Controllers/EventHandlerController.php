<?php
namespace App\Controllers;

use App\Core\Controller;
use App\EventHandlers\EmailEventHandler;
use App\Models\EventModel;
use PHPMailer\PHPMailer\PHPMailer;

class EventHandlerController extends Controller{
    public function handle(string $type){
      
        $eventModel = new EventModel($this->pdo);
        $events = $eventModel->getAllByTypeAndStatus($type,'pending');

        if(!count($events)){
            return;
        }
        if($type== 'email'){
            $this->handleEmails($events);
        }

    }
    public function handleById(string $type , string $id){
        
        $eventModel = new EventModel($this->pdo);
        $id = (int)$id;
        $event = $eventModel->getByTypeAndStatusAndId($type,'pending', $id);
       
        if(!$event){
            echo json_encode($event);
            return;
        }
        if($type== 'email'){
            $this->handleEmailEvent($event);
            
        }

    }
      private function handleEmails(array $events){
         foreach($events as $event){
             $this->handleEmailEvent($event);
         }
    }
    private function handleEmailEvent($event){
        $mail = new PHPMailer(true);
        $emailEventHandler= new EmailEventHandler($mail);
        $eventModel = new EventModel($this->pdo);

        $emailEventHandler->setData($event->data);


        $eventModel->editById($event->event_id ,[
            'status' => "started"
        ]);

        $newStatus = $emailEventHandler->handle();
   
        $eventModel->editById($event->event_id ,[
            'status' =>  $newStatus
        ]);

      
        echo "Done for event" . $event->event_id . "with status $newStatus <Br>";
    }
}