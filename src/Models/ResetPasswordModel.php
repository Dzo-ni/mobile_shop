<?php
namespace App\Models;
 
use App\Core\Field;
use App\Core\Config;
use App\Core\Model; 
use App\Validators\BitValidator;
use App\Validators\NumberValidator;
use App\Validators\StringValidator;

class ResetPasswordModel extends Model
{
    protected function getFields(): array
    {
        return [
            'reset_password_id'=> new Field((new NumberValidator())->setIntegerLength(11),false),
            'email_id'=> new Field((new NumberValidator())->setIntegerLength(11)),
            'token'=> new Field((new StringValidator())->setMaxLength(32)),
            'is_active'=> new Field((new BitValidator))
            
        ];
    }
    public function getByEmailIdAndActiveRow($email_id) {
        $query = "select * from reset_password where email_id = ? and is_active = 0 order by reset_password_id desc limit 1";
        $stmt = $this->pdo->prepare($query);
        $result = null;
        if( $stmt->execute([$email_id])){
            $result = $stmt->fetch(\PDO::FETCH_OBJ);
        }
       
        return $result;
    }
}


    
