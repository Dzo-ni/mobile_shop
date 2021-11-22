<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProductModel;
use App\Models\StockModel;
use Exception;

class ProductController extends Controller{
    
        public function create(){
          
  
if(isset($_POST, $_FILES)) {
    $data = $_FILES;
    $target_dir = "assets/img/product_images/";
    
$target_file = $target_dir . basename($data["product_image"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
   

  $check = getimagesize($data["product_image"]["tmp_name"]);
  if($check === false || ($imageFileType !== "jpg" && $imageFileType !== "png" && $imageFileType !== "jpeg")) {
    $response['msg']= "File is not an image.";
    $response['status']= 0;
    die(json_encode($response)) ;
  }}
  if ($data["product_image"]["size"] > 2097152) {
    $response['msg']= "File is too large.File must be lower then 2MB";
    $response['status']= 0;
    die(json_encode($response)) ;
  }
            $prefix_name= microtime(true).".".date("d.m.Y");
            $originalFileName= $prefix_name.basename($data["product_image"]["name"]);
         
            $fileName=$target_dir. $originalFileName;
            if (move_uploaded_file($data["product_image"]["tmp_name"], $fileName)) {
                $response['msg']= "The file ". htmlspecialchars( basename( $_FILES["product_image"]["name"])). " has been uploaded.";
                $response['status']= 1;
             
                //die(json_encode($response)) ;
            } else {
                $response['msg']= "There was an error uploading your file.";
                $response['status']= 0;
                die(json_encode($response)) ;
            }

            if(!isset($_POST['product_brand'],$_POST['product_name'],$_POST['product_price'],$_POST['product_camera'],$_POST['product_ram'])){
              $response['msg']= "All fields must be filled.";
              $response['status']= 0;
              die(json_encode($response));
            }
            $product['product_brand'] = filter_var(trim($_POST['product_brand']),FILTER_SANITIZE_STRING);
            $product['product_name'] = filter_var(trim($_POST['product_name']),FILTER_SANITIZE_STRING);
            $product['product_price'] = filter_var(trim($_POST['product_price']),FILTER_SANITIZE_NUMBER_FLOAT);
            $product['product_camera'] = filter_var(trim($_POST['product_camera']),FILTER_SANITIZE_NUMBER_INT);
            $product['product_ram'] = filter_var(trim($_POST['product_ram']),FILTER_SANITIZE_NUMBER_INT);
            $product['product_storage'] = filter_var(trim($_POST['product_storage']),FILTER_SANITIZE_NUMBER_INT);

            foreach($product as $value){
           if(empty($value)){
            $response['msg']= "All fields must be valid.";
            $response['status']= 0;
            die(json_encode($response));
           }
          }
           



          $path= file_get_contents($fileName);
           
          $image = imagecreatefromstring($path);
         
          $oldW= imagesx($image);
          $oldH= imagesy($image);

          $newW=200;
          $newH=350;
          
  

          $newThumbnail = imagecreatetruecolor($newW,$newH);


            // Set the background color of image using
            // imagecolorallocate() function.
            $bg = imagecolorallocate($newThumbnail, 255, 255, 255);
            // Fill background with above selected color.
           imagefill($newThumbnail, 0, 0, $bg); 
        
       
          $font_path = realpath('font.ttf');
       

        // Print Text On Image
      // echo json_encode(imagettftext($newThumbnail, 25, 0, 0, 0, $bg, $font_path, $text));

        // Add text using a font from local file
        $dataArr = imagettftext($newThumbnail, 20, 0, 30, 330,
        imagecolorallocate($newThumbnail, 0, 0, 0),
        $font_path, 'MOBILE SHOP');


       imagecopyresampled($newThumbnail, $image , 0,0,0,0,$newW,$newH,$oldW,$oldH);



          $thumbnail="_thumbnail".$prefix_name.basename($data["product_image"]["name"]);
          $location ="./assets/img/product_images/";
          if($data["product_image"]["type"] == "image/jpeg"){
              
            imagejpeg($newThumbnail , $location.$thumbnail);
           
          }else{
            imagepng($newThumbnail ,  $location.$thumbnail);
           
          }
          
          // free memory
            imagedestroy($newThumbnail);
                //$fileName
               
                $product['product_image'] = $originalFileName;
          
                $product['product_thumbnail'] =$thumbnail;
             
               
                $productModel = new ProductModel($this->pdo);
               
                $product_id =$productModel->add($product);
               
              
               
                $stock["quantity"] = $_POST['quantity'];
                $stock['product_id'] = $product_id;
             
                $stockModel = new StockModel($this->pdo);
                 $stockModel->add($stock);
              $response['status'] = 1;
              $product['product_id'] =$product_id;
           
              $product['quantity'] = $stock["quantity"];
              $product['created_at'] = date("Y-m-d H:i:s");
              $product['product_status'] = 1;
              $product['exposed'] = 0;

             $response['product'] = $product;
              echo  json_encode($response);
              die;
           
        
        }
        
        public function latestFour(){
            $productModel = new ProductModel($this->pdo);
            $latest_four=$productModel->getLatestFour();
            // $query = "SELECT * FROM product WHERE exposed = 1 order by product_id DESC limit 4 ";
           
            echo json_encode($latest_four);
        }

        public function relatedFour($id){
            $productModel = new ProductModel($this->pdo);
            $latest_four=$productModel->getRelatedFour($id);
            echo json_encode($latest_four);
        }
   
        public function getAll(){
            $json = file_get_contents("php://input");
            $data = json_decode($json,true);
            $productModel = new ProductModel($this->pdo);
            $products= $productModel->getAllByFiltering($data);
           
            echo json_encode($products);
        
    }
    public function getAllToDasboard(){
        $stockModel = new StockModel($this->pdo);
        $products= $stockModel->getAllProductsAndQuantity();
        echo json_encode($products);
    
}
  
    public function show(int $id){
        $productModel = new ProductModel($this->pdo);
        $product=$productModel->getById($id);
        echo json_encode($product);
    }

    public function showToDashboard(int $id){
      $stockModel = new StockModel($this->pdo);
      $product=$stockModel->getProductAndQuantity($id);
      echo json_encode($product);
  }

  public function update(int $id){
    $data = file_get_contents('php://input');
    $data = json_decode($data);
    $product=[];
    $product_status=[];
    $stock=[];
    foreach($data->product as $property=>$value){
      if(  $property == "created_at" || $property == "product_id" || $property == "stock_id")continue;
      if($property == "quantity") {
        $stock[$property] = $value;
    }else{
      if($property == "product_status" || $property == "exposed" ){
        $product[$property] = (int)$value;
        continue;
      }
      $product[$property] = $value;
    }
  }
  
    $stock['modified_at'] = date("Y-m-d H:i:s",time()+3600);
    
    $productModel = new ProductModel($this->pdo);
    $stockModel = new StockModel($this->pdo);
    $productModel->editById($id, $product);
    $stockModel->editById($id, $stock);
  }

  public function delete(int $id){
    $productModel = new ProductModel($this->pdo);
    $stockModel = new StockModel($this->pdo);
    $stock = $stockModel->getByFieldName("product_id", $id);
    if($stockModel->deleteById($stock->stock_id))
    echo $productModel->deleteById($id);
  }
}