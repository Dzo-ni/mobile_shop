<?php
namespace App\Models;

use App\Core\Config;
use App\Core\Field;
use App\Core\Model; 
use App\Validators\BitValidator;
use App\Validators\NumberValidator;
use App\Validators\StringValidator;
use App\Validators\DateTimeValidator;

class EmailModel extends Model{
    protected function getFields():array{
        return [
            'email_id'=> new Field((new NumberValidator())->setIntegerLength(11),false),
            'email_address'=> new Field((new StringValidator())->setMaxLength(100)),
            'activation_code'=> new Field((new StringValidator())->setMaxLength(255)),
            'email_status_id'=> new Field(new BitValidator()),
            'on_mailing'=> new Field(new BitValidator()),
            'created_at' => new Field((new DateTimeValidator())->allowDate()
                                                                ->allowTime(),false),
            'modified_at' => new Field((new DateTimeValidator())->allowDate()
                                                                ->allowTime(),false),
            'is_customer_registered'=> new Field(new BitValidator()),
            'customer_id'=>new Field((new NumberValidator())->setIntegerLength(11))
            
        ];
    }

    public function checkEmailAdressForUser($email_adress){
        $query = "select email_id from email where email_address = ? and is_customer_registered = ?";
        $stmt= $this->getConnection()->prepare( $query);
        $stmt->execute([$email_adress, Config::CUSTOMER_REGISTERED]);
        return $stmt->fetchColumn();
    }

    public function checkEmailAdressForCustomer($email_adress){
        $query = "select email_id from email where email_adress = ? and is_customer_registered = ?";
        $stmt= $this->pdo->prepare( $query);
        $stmt->execute([$email_adress, Config::CUSTOMER_NOT_REGISTERED]);
        return $stmt->fetchColumn();
    }


    public function checkUserEmail($email = null)
    {
        $this->email =  $this->email ?? $email;
        $response = true;
        $query = "SELECT email_id FROM email WHERE email_address = ? and is_customer_registered = 1";

        

        $stmt = $this->pdo->prepare($query);
        //execute
        $stmt->execute([$this->email]);

        if ($stmt->fetchColumn() > 0) {
            $response = false;
        }
        return  $response;
    }
    public function getCustomerIdByEmailAddress($email_address){
        $query = "select customer_id from email where email_address = ?";
        $stmt=$this->pdo->prepare($query);
        $customer_id = null;
        if($stmt->execute([$email_address])){
            $customer_id=$stmt->fetchColumn();
        }
        return $customer_id;
    }
    public function verify($email_address, $activation_code){
        $response['status']=0;
        $query = "update  email set email_status_id = 2 where email_address = ? and activation_code = ?";
        $stmt=$this->pdo->prepare($query);
        $stmt->execute([$email_address ,$activation_code]);
        if($stmt->rowCount()>0){
           $query = "update user set user_status_id = 2";
           if($this->pdo->exec($query)) $response['status']=1;
        }
        return $response;
    }

    public function getUserId($email){
        $query = "select u.user_id from user u join email e on u.customer_id= e.customer_id where e.email_address= ?";
        $stmt= $this->pdo->prepare($query);
        $stmt->execute([$email]);
        return $stmt->fetchColumn();
    }
}