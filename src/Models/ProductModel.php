<?php
namespace App\Models;
 
use App\Core\Field;
use App\Core\Model; 
use App\Validators\BitValidator;
use App\Validators\NumberValidator;
use App\Validators\StringValidator;
use App\Validators\DateTimeValidator;

class ProductModel extends Model{
    protected function getFields():array{
        return [
            'product_id'=> new Field((new NumberValidator())->setIntegerLength(11),false),
            
            'product_name'=> new Field((new StringValidator())->setMaxLength(120)),
            'product_brand'=> new Field((new StringValidator())->setMaxLength(100)),
            'product_price'=> new Field((new NumberValidator)->setDecimal()
                                                              ->setUnsigned()
                                                              ->setIntegerLength(6)
                                                              ->setMaxDecimalDigits(2)),
            'product_ram' => new Field((new StringValidator)->setMaxLength(5)),
            'product_storage'=>new Field((new StringValidator)->setMaxLength(50)),
            'product_camera'=>new Field((new StringValidator)->setMaxLength(20)),
            'product_image'=>new Field((new StringValidator)),
            'product_thumbnail'=>new Field((new StringValidator)),
            'product_status'=>new Field(new BitValidator()),
            'exposed'=>new Field(new BitValidator()),                                           
            'stock_id'=> new Field((new NumberValidator())->setIntegerLength(11)),
           
        ];
    }

    // public function getAllByCategoryId(int $order_id){
    //     return $this->getAllByFieldName('order_id',$order_id);
    // }

    public function getAllByFiltering($data){
        $query = "SELECT * FROM product WHERE product_status = '1' ";
    
        if (
            isset($data['minimum_price'], $data['maximum_price']) && !empty($data['minimum_price'])
            && !empty($data['maximum_price'])
        ) {
            $query .= " AND product_price BETWEEN '" . $data['minimum_price'] . "' AND '" . $data['maximum_price'] . "'";
        }
       
        $query= $this->concatinateConditions('brand', $query,$data);
        $query= $this->concatinateConditions('ram', $query,$data);
        $query= $this->concatinateConditions('storage', $query,$data);
      
        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    private function &concatinateConditions($field, $query,$data) {
        if (isset($data[$field]) && !empty($data[$field])) {
            $brand_filter = implode("','", $data[$field]);
            $query .= " AND product_".$field." IN ('" . $brand_filter . "')";
           
        }
        return $query;
    }
    public function getLatestFour(){
        // $query = "SELECT * FROM product WHERE exposed = 1 order by product_id DESC limit 4 ";
        $latest_four= $this->getAllByFieldName("exposed", 1);
        $this->orderByDesc($latest_four,"product_id");
        return array_slice($latest_four,0,4);
    }
    public function getRelatedFour($id){

       
           $product=$this->getById($id);
           return $this->proccessRelatedFour($product,$id);
        
      
    }
    private function proccessRelatedFour($product,$id){
        $query = "SELECT * FROM product WHERE (product_brand = ?
                                                OR product_ram = ?
                                                OR product_storage = ? )
                                                AND product_id <> ?";
        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute([$product-> product_brand ,  $product-> product_ram , $product-> product_storage, $id]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }



    public function getAllByProductStatus($field){
       $filters= $this->getAllByFieldName("product_status", "1" );
       $this->orderByDesc($filters,$field);
        $fields=[];
        foreach($filters as $filter){
            if(!in_array($filter->{$field},$fields))
                $fields[]=$filter->{$field};
        }
        return $fields;
    }

    private function &orderByDesc($items,$field){
        $filters = usort($items, function($a, $b) use($field) { 
            return strcmp($b->{$field}, $a->{$field});
        });
        return $filters;
    }
}