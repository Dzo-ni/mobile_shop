<?php
namespace App\Models;
 
use App\Core\Field;
use App\Core\Model; 
use App\Validators\BitValidator;
use App\Validators\NumberValidator;
use App\Validators\StringValidator;
use App\Validators\DateTimeValidator;

class OrderDetailModel extends Model{
    protected function getFields():array{
        return [
            'order_detail_id'=> new Field((new NumberValidator())->setIntegerLength(11),false),
            'quantity'=> new Field((new NumberValidator())->setIntegerLength(11)),
            'product_id'=> new Field((new NumberValidator())->setIntegerLength(11)),
            'order_id'=> new Field((new NumberValidator())->setIntegerLength(11)),
        ];
    }

    public function insertOrderDetail($shopping_card,$order_id){
                //product_id
                //order_id
                //quantity
              
                $products_ids = $this->get_product_property($shopping_card ,"id");
                $products_quantities = $this->get_product_property($shopping_card ,"quantity");
              
                  //order_detail     quantity product_id order_id  
                $query= "insert into order_detail(quantity , product_id , order_id  ) VALUES(?, ?, ?)";
                $stmt=$this->pdo->prepare($query);
               
              
                    for($i=0;$i< sizeof($products_quantities);$i++){
                      $stmt->execute([ $products_quantities[$i], $products_ids[$i], $order_id ]);
                }
               
    }

    

    public function getInformation($order_id){
        $query= "select p.product_name, od.quantity from product p 
        join order_detail od on p.product_id = od.product_id
        where od.order_id = ? group by p.product_name";
        $stmt = $this->pdo->prepare($query);
        $res = $stmt->execute([$order_id]);
        $order_details_arr = [];
        if($res){
            $order_details_arr = $stmt->fetchAll(\PDO::FETCH_OBJ);
        }
        return $order_details_arr;
    }



    private function get_product_property($shopping_card, $type){
        $properties=[];
        $suppose_type="id";
        if($type=="quantity") $suppose_type = "quantity";

        foreach($shopping_card as $item){
            if($suppose_type == "id"){
                $properties[]=(int)$item->product_id;
            }else{
                $properties[]=$item->quantity;
            }
         
        }
        return $properties;
    }


    // public function getInformationToAdmin($orderId){
    //     $query = 
    // }
}