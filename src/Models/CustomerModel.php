<?php
namespace App\Models;
 
use App\Core\Field;
use App\Core\Model; 
use App\Validators\BitValidator;
use App\Validators\NumberValidator;
use App\Validators\StringValidator;
use App\Validators\DateTimeValidator;

class CustomerModel extends Model{
    protected function getFields():array{
        return [
            'customer_id'=> new Field((new NumberValidator())->setIntegerLength(11),false),
            'firstname'=> new Field((new StringValidator())->setMaxLength(45)),
            'lastname'=> new Field((new StringValidator())->setMaxLength(45)),
            'stripe_customer_id'=> new Field((new StringValidator())->setMaxLength(100)),
        ];
    }

    public function getStripeCustomerId($field , $type){
     
        if($type == 'user_id'){
            $query ="SELECT stripe_customer_id FROM customer c
            join user u on c.customer_id = u.customer_id
            WHERE user_id = ?";
        }elseif ($type == 'email_address') {
            $query ="SELECT stripe_customer_id FROM customer c
            join email e on c.customer_id = e.customer_id WHERE e.email_address = ?";
        }
     
        $stmt = $this->pdo->prepare($query);
      
        $stmt->execute([$field]);
        $stripeCustomerID = $stmt->fetchColumn() ?? null;
 
        return $stripeCustomerID;
    }




    public function getAllByCategoryId(int $order_id){
        return $this->getAllByFieldName('order_id',$order_id);
    }
}