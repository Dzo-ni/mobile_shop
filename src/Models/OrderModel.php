<?php
namespace App\Models;
 
use App\Core\Field;
use App\Core\Config;
use App\Core\Model; 
use App\Validators\BitValidator;
use App\Validators\NumberValidator;
use App\Validators\StringValidator;
use App\Validators\DateTimeValidator;

class OrderModel extends Model{
    protected function getFields():array{
        return [
            'order_id'=> new Field((new NumberValidator())->setIntegerLength(11),false),
            'po_number'=> new Field((new StringValidator())->setMaxLength(45)),
            'stripe_pi'=> new Field((new StringValidator())->setMaxLength(255)),
            'subtotal_price'=> new Field((new NumberValidator)->setDecimal()
                                                              ->setUnsigned()
                                                              ->setIntegerLength(10)
                                                              ->setMaxDecimalDigits(2)),
            'total_price' => new Field((new NumberValidator)->setDecimal()
                                                            ->setUnsigned()
                                                            ->setIntegerLength(10)
                                                            ->setMaxDecimalDigits(2)),
            'order_status_id'=> new Field(new BitValidator()),
            'created_at' => new Field((new DateTimeValidator())->allowDate()
                                                                ->allowTime()),
            'modified_at' => new Field((new DateTimeValidator())->allowDate()
                                                                ->allowTime()),
            'customer_id'=> new Field((new NumberValidator())->setIntegerLength(11))
            
        ];
    }

  
        private $product_id;
        private $sales_id;
        private $quantity;
    
        public function __construct(&$pdo)
        {
            $this->pdo = $pdo;
        }

        public function updateStatus($isSuccess, $pi){
            if ($isSuccess) {
               
                $query= "update `order` set status = ".Config::ORDER_CONFIRMED." where stripe_pi ='$pi'";
               
            } else {
                $query= "update `order` set status = ".Config::ORDER_FAILED." where stripe_pi = '$pi'";
            }
            
            $this->pdo->exec($query);
        }
    
        public function returnToStock($paymentIntent_id){
            $query= "update product p join 
            order_detail od on p.product_id = od.product_id
            join sales s on od.sales_id = s.sales_id 
            set p.product_quantity = p.product_quantity + od.quantity 
            where s.stripe_pi = '$paymentIntent_id'";
        }
      
     //order||    po_number stripe_pi  subtotal_price total_price
        //            order_status_id stripe_customer_id
    public function insertOrder($po_number, $stripe_pi, $subtotal_price, $total_price, $order_status_id, $customer_id){
            //user_id
            //total_price
              //stripe_pi
            $query = "insert into `order`(po_number , stripe_pi , subtotal_price , total_price, order_status_id, customer_id)
             VALUES(?, ? ,?, ?,?, ?)";
           
            $stmt= $this->pdo->prepare($query);
            $stmt->execute([$po_number, $stripe_pi, $subtotal_price, $total_price, $order_status_id, $customer_id]);
        
            return $this->getConnection()->lastInsertId();
             
    }
    
    
    
    
    public function getTotalSumOrder(){
        $query= "select sum(total_price) from `order`";
        $res = $this->pdo->query($query);
        return $res->fetchColumn();
    }
    
    
    public function getAllInformationToAdmin($order=false, $order_id = null){
       
        if($order){
            $query= 'SELECT o.order_id, o.subtotal_price, o.total_price , os.name as status, concat(c.firstname , " " , c.lastname) as "created_by" , o.created_at from `order` o 
            inner join order_status os on os.order_status_id = o.order_status_id
            inner join customer c on c.customer_id = o.customer_id';
            $query.= " where o.order_id = ? limit 1";

            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$order_id]);
            $order = $stmt->fetch(\PDO::FETCH_OBJ);
            return $order ?? null;
        }else{
            $query= 'SELECT o.order_id, o.subtotal_price, o.total_price , os.name as status, concat(c.firstname , " " , c.lastname) as "created_by" , o.created_at from `order` o 
            inner join order_status os on os.order_status_id = o.order_status_id
            inner join customer c on c.customer_id = o.customer_id order by order_id desc';

            $stmt = $this->pdo->prepare($query);
            $stmt->execute([]);
            $orders = $stmt->fetchAll(\PDO::FETCH_OBJ);
            return $orders ?? [];
        }
       
    }



    
    
          
        // public function getAllByCategoryId(int $order_id){
    //     return $this->getAllByFieldName('order_id',$order_id);
    // }
    }





    
