<?php
namespace App\Models;
 
use App\Core\Field;
use App\Core\Model; 
use App\Validators\BitValidator;
use App\Validators\NumberValidator;
use App\Validators\StringValidator;
use App\Validators\DateTimeValidator;

class EventModel extends Model{
    protected function getFields():array{
        return [
            'event_id'=> new Field((new NumberValidator())->setIntegerLength(20),false),
            'type'=> new Field((new StringValidator())->setMaxLength(45)),
            'data'=> new Field((new StringValidator())->setMaxLength(64 * 1024)),
            'created_at' => new Field((new DateTimeValidator())->allowDate()
                                                                ->allowTime(),false),
            'modified_at' => new Field((new DateTimeValidator())->allowDate()
                                                                ->allowTime(),false),
            'status'=> new Field((new StringValidator())->setMaxLength(45)),
        ];
    }

    public function getAllByTypeAndStatus(string $type,string $status){
        $query = "select * from `event` where type = ? and status = ?";
        $stmt=$this->pdo->prepare($query);
        $res = $stmt->execute([$type,$status]);
        if(!$res) return [];
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
    public function getByTypeAndStatusAndId(string $type,string $status , int $event_id){
       
        $query = "select * from `event` where type = ? and status = ? and event_id = ?";
        $stmt=$this->pdo->prepare($query);
      
        $res = $stmt->execute([$type,$status , $event_id]);
        
        if(!$res) return [];
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

}