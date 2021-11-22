<?php
require_once "config.php";
require_once "vendor/autoload.php";

use App\Models\ProductSalesModel;
use App\Core\DBConnection;



$db = new DBConnection();
$productSalesModel = new ProductSalesModel($db->getConnection());

$json = file_get_contents('php://input');
$data = json_decode($json);

function calculateOrderAmount( $items): int {
    // Replace this constant with a calculation of the order's amount
    // Calculate the order total on the server to prevent
    // customers from directly manipulating the amount on the client
    $total=0.0;
    foreach($items as $item){
        $total += floatval($item->product_price) * floatval($item->quantity);
    }
    return round(floatval(($total /100) * 100),2);
  }


//parse JSON write check stock if exist enough then reduce product quantity and write row in sales and product_sales
//and then continue to card proceessing
$shopping_card = json_decode($data->shopping_card);

$response= $productSalesModel->checkQuantities($shopping_card);

//processing payment on stripe if quantites exist
//   //doesnt have on 
if(!$response['success']){
    echo json_encode($response);

    die;
}
//decrease product_quantity;
//sales
//pruduct_sales
$subtotal= calculateOrderAmount($shopping_card);
$total_price= round(floatval($subtotal)+floatval($subtotal /20) ,2);

if($data->session != ""){
    session_start();
    $user_id = $_SESSION['user_id'];
}else{
    //user id if user not register
}


echo json_encode($productSalesModel->insertSalesAndDetails($user_id, $subtotal ,$total_price, $data->stripe_pi, $shopping_card));
die;
