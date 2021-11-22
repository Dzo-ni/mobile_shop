<?php
namespace App\Models;
 
use App\Core\Field;
use App\Core\Model; 
use App\Validators\BitValidator;
use App\Validators\NumberValidator;
use App\Validators\StringValidator;
use App\Validators\DateTimeValidator;

class UserModel extends Model{
    private $user;
   
    protected function getFields():array{
        return [
            'user_id'=> new Field((new NumberValidator())->setIntegerLength(11),false),
           
            'password'=>new Field((new StringValidator())->setMaxLength(255)),
            'user_status_id'=> new Field(new BitValidator()),
            'user_role_id'=> new Field((new NumberValidator())->setIntegerLength(1)),
            'created_at' => new Field((new DateTimeValidator())->allowDate()
                                                                ->allowTime(),false),
            'modified_at' => new Field((new DateTimeValidator())->allowDate()
                                                                ->allowTime(),false),
            'customer_id'=> new Field((new NumberValidator())->setIntegerLength(11))
            
        ];
    }
    

    public function create($user){
        //upisi usera u db
        $this->add($user);
    }

    public function checkPasswordViaEmailAddress($email_address, $password)
    {
        $query = "SELECT u.user_id user_id, u.password user_password, 
        e.email_address 
        FROM email e 
        INNER JOIN customer c ON e.customer_id = c.customer_id
        INNER JOIN user u ON c.customer_id = u.customer_id
        WHERE e.email_address = ? ";

       
        $stmt = $this->pdo->prepare($query);
        //execute
        $stmt->execute([$email_address]);
   
        $response['msg'] = "User doesn't exist";
        $user = $stmt->fetch(\PDO::FETCH_OBJ);
        $response['status']=0;
        if($user){
            if (password_verify($password, $user->user_password)) {
             
                $response['status'] = 1;
                $response['msg'] = "";
                $response['user_id']= $user->user_id;
                   
                // $response["user"]['user_id'] = $result->user_id;
                // $response["user"]['firstname'] =$result->firstname;
                // $response["user"]['lastname'] =$result->lastname;
                // $response["user"]['email'] =$result->email;
                // $response["user"]['user_status'] =$result->user_status;
                
                //open session
                // session_start();
        }  
    }
            return $response;
}
    public function getStripeIdById(int $user_id){
        $query = "select stripe_customer_id from customer c join user u on c.customer_id = u.customer_id  where u.user_id = ?";
        $stmt=$this->pdo->prepare($query);
        $stripe_customer_id = null;
        if($stmt->execute([$user_id])){
            $stripe_customer_id=$stmt->fetchColumn();
        }
        return $stripe_customer_id;
    }
    public function getCustomerIdById($user_id){
       
        $query = "select c.customer_id from customer c join user u on c.customer_id = u.customer_id  where u.user_id = ?";
        $stmt=$this->pdo->prepare($query);
        $customer_id = null;
        if($stmt->execute([$user_id])){
            $customer_id=$stmt->fetchColumn();
        }
        return $customer_id;
    }

    public function getPersonalInformationForUser($user_id){
        $query= "select c.firstname, c.lastname, e.email_address, e.email_status_id , a.street, a.city from customer c inner join
        user u on c.customer_id = u.customer_id 
        inner join email e on c.customer_id = e.customer_id
        left join address a on a.customer_id = c.customer_id where u.user_id = ?";
       
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(\PDO::FETCH_OBJ);
        return $user ?? null;
     
    }

    public function getAllPersonalInformationToAdmin($user=false, $customer_id = null){
        $query= "select c.customer_id,  c.firstname, c.lastname, e.email_address , ur.name as user_role , us.name user_status from customer c inner join
        user u on c.customer_id = u.customer_id 
        inner join email e on c.customer_id = e.customer_id
        inner join user_role ur on u.user_role_id = ur.user_role_id
        inner join user_status us on u.user_status_id = us.user_status_id" ;
        if($user){
            $query.= " where c.customer_id = ? limit 1";
        }
        $stmt = $this->pdo->prepare($query);
    
        if($user){
            $stmt->execute([$customer_id]);
            $user = $stmt->fetch(\PDO::FETCH_OBJ);
            return $user ?? null;
        }
        else{
            $stmt->execute([]);
            $users = $stmt->fetchAll(\PDO::FETCH_OBJ);
        
            return $users ?? [];
        }
       
    }


    public function getByIdAndRole($user_id , $user_role_id ){
      
        $sql = "select user_id from user where user_id = ? and user_role_id = ? limit 1";
       
        $stmt= $this->pdo->prepare($sql);
        $res= $stmt->execute([$user_id , $user_role_id]);
      
            return $stmt->fetchColumn();
       
    }
    
}       