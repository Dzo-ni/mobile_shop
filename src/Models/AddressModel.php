<?php
namespace App\Models;
 
use App\Core\Field;
use App\Core\Model; 
use App\Validators\BitValidator;
use App\Validators\NumberValidator;
use App\Validators\StringValidator;
use App\Validators\DateTimeValidator;

class AddressModel extends Model{
    protected function getFields():array{
        return [
            'address_id'=> new Field((new NumberValidator())->setIntegerLength(11),false),
            'street'=> new Field((new StringValidator())->setMaxLength(45)),
            'city'=> new Field((new StringValidator())->setMaxLength(45)),
            'customer_id'=> new Field((new NumberValidator())->setIntegerLength(11))
            
        ];
    }

    public function getAllByCategoryId(int $order_id){
        return $this->getAllByFieldName('order_id',$order_id);
    }

    public function updateOrInsert(int $customer_id, array $data){
     
        if($current_address=$this->getByFieldName("customer_id", $customer_id)) {
            if(isset($data['city'], $data['street']) && !empty(trim($data['city']))  && !empty(trim($data['street'])) ){
                $address= [];
                $address['city'] = $data['city'];
                $address['street'] = $data['street'];
                $address['customer_id'] = $customer_id;
                   return $this->editById($current_address->address_id, $address);
            }
      
        }else{

            if(isset($data['city'], $data['street']) && !empty(trim($data['city']))  && !empty(trim($data['street'])) ){
                $address= [];
                $address['city'] = $data['city'];
                $address['street'] = $data['street'];
                $address['customer_id'] = $customer_id;
            }
            return $this->add($address);
        }
             return false;
    }

   
    
}