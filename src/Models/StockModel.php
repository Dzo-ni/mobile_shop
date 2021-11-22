<?php
namespace App\Models;
 
use App\Core\Field;
use App\Core\Model; 
use App\Validators\BitValidator;
use App\Validators\NumberValidator;
use App\Validators\StringValidator;
use App\Validators\DateTimeValidator;

class StockModel extends Model{
    protected function getFields():array{
        return [
            'stock_id'=> new Field((new NumberValidator())->setIntegerLength(11),false),
            'quantity'=> new Field((new NumberValidator())->setIntegerLength(11)),
            'created_at' => new Field((new DateTimeValidator())->allowDate()
                                                                ->allowTime(),false),
            'modified_at' => new Field((new DateTimeValidator())->allowDate()
                                                                ->allowTime()),
            'product_id' => new Field((new NumberValidator())->setIntegerLength(11)),
        ];
    }

    public function getAllProductsNameAndQuantity(){
        $query= "select p.product_name, s.quantity from product p 
        join stock s on p.product_id = s.product_id
        group by p.product_name";
        $stmt = $this->pdo->prepare($query);
        $res = $stmt->execute();
        $order_details_arr = [];
        if($res){
            $order_details_arr = $stmt->fetchAll(\PDO::FETCH_OBJ);
        }
        return $order_details_arr;
    }

    public function getAllProductsAndQuantity(){
        $query= "select p.product_id, p.product_name , p.product_price , s.quantity , s.created_at, p.product_status , p.exposed from product p 
        join stock s on p.product_id = s.product_id
        group by p.product_name";
        $stmt = $this->pdo->prepare($query);
        $res = $stmt->execute();
        $order_details_arr = [];
        if($res){
            $order_details_arr = $stmt->fetchAll(\PDO::FETCH_OBJ);
        }
        return $order_details_arr;
    }
    public function getProductAndQuantity($id){
        $query= "select p.product_id, p.product_name, p.product_ram, p.product_storage,  p.product_camera,  p.product_price , s.quantity , s.created_at, p.product_status , p.exposed , p.product_image from product p 
        join stock s on p.product_id = s.product_id where p.product_id=  ?
        group by p.product_name";
        $stmt = $this->pdo->prepare($query);
        $res = $stmt->execute([$id]);
        $product_details = null;
        if($res){
            $product_details = $stmt->fetch(\PDO::FETCH_OBJ);
        }
        return $product_details;
    }
   
    public function checkQuantities($shopping_card){
        // $response['success'] =false;
        // $response['missing']= $product_name;
                foreach($shopping_card as $item){
        
                    //suppose that we have all needs quantities
                    $response['success'] =true;
                    //check stock if exist enough
                    // SELECT product_id from product where product_quantity >= 6 and product_id = 1
                    $query= "SELECT * from product p
                    join stock s on p.product_id = s.product_id where p.product_id =  ?";
                    $stmt= $this->pdo->prepare($query);
                    $stmt->execute([$item->product_id]);
                   
                        $product = $stmt->fetch(\PDO::FETCH_OBJ);
                        if ((int)$product->quantity < $item->quantity){
                            $response['success'] =false;
                            // $query= "SELECT product_name from product where product_id =  ?";
                            // $stmt= $this->pdo->prepare($query);
                            // $stmt->execute([$item->product_id]);
                            $response['missing_product']= $product;
                            break;
                        }
                    }
                   $response["needs"] =$item->quantity;
                   $response['have']  = (int)$product->quantity;
                     return $response;
                }
}