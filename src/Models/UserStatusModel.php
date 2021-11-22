<?php
namespace App\Models;
 
use App\Core\Field;
use App\Core\Model; 
use App\Validators\BitValidator;
use App\Validators\NumberValidator;
use App\Validators\StringValidator;
use App\Validators\DateTimeValidator;

class UserStatusModel extends Model{
    protected function getFields():array{
        return [
            'user_status_id'=>new Field(new BitValidator()),
            'name'=> new Field((new StringValidator())->setMaxLength(45)),
        ];
    }

    public function getAllByCategoryId(int $order_id){
        return $this->getAllByFieldName('order_id',$order_id);
    }
}