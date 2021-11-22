<?php

namespace App\Controllers;

use App\Controllers\Customer\StripeCustomer;
use App\Controllers\Order\OrderEmailController;
use App\Controllers\Order\SignInOrderController;
use App\Controllers\Payment\StripePaymentIntent;
use App\Core\Config;
use App\Core\Controller;
use App\Models\AddressModel;
use App\Models\CustomerModel;
use App\Models\UserModel;
use App\Models\EmailModel;
use App\Models\OrderModel;
use App\Models\StockModel;


class OrderController extends Controller
{

    public function order()
    {
    $json = file_get_contents('php://input');
    $data = json_decode($json);

    //parse JSON write check stock if exist enough then reduce product quantity and write row in sales and product_sales
    //and then continue to card proceessing
    
    //call product inventory model
    $shopping_card = json_decode($data->shopping_card);
    $stockModel = new StockModel($this->pdo);
    $response= $stockModel->checkQuantities($shopping_card);
   
   
    if(!$response['success']){
        echo json_encode($response);
    
        die;
    }
    
    $subtotal= $this->calculateOrderAmount($shopping_card);
    $total_price= round(floatval($subtotal)+floatval($subtotal /20) ,2);
    $po_number = uniqid("ponumber_",true);  
  
        if($this->getSession()->has('user_id')){
        $user_id= $this->getSession()->get("user_id");
      
        $stripe_pi = $data->stripe_pi;
        //processing payment on stripe
        // $paymentIntent= new StripePaymentIntent();
        // $paymentIntent->confirm($stripe_pi);
       
        $userModel = new UserModel($this->pdo);
        $customer_id=$userModel->getCustomerIdById($user_id);
        try {
          
            $this->pdo->beginTransaction();
            $signInOrder = new SignInOrderController($this->pdo);
            $order=$signInOrder->insertOrder($po_number, $stripe_pi,  $subtotal ,$total_price, $customer_id , $shopping_card);
         
         $orderEmail = new OrderEmailController($this->pdo);

           $event_id = $orderEmail->create($order['firstname'], $order['lastname'], $order['order']->po_number ,
                    $order["order"]->subtotal_price,  $order["order"]->total_price, 
                    $order['email_address'] ,   $order["order_detail_array"]);
               
                    
               
            if($event_id){
                $response['event_id']= $event_id;
            }

            //response['event_id']= event
            $this->pdo->commit();
            $response['success']= true;
            } catch (\Throwable $th) {
                $this->pdo->rollback();
                $response['success']= false;
            }
            
            echo json_encode($response);
    }else{
      
           $customer = $data->customer;
           $stripe_pi = $data->stripe_pi;
           
        
           
           $po_number = uniqid("ponumber_",true);  
        //    $uniq_id= uniqid().uniqid();
        //    $stripeCustomer= new StripeCustomer($this->pdo);
        //    $stripe_customer_id = $stripeCustomer->create($uniq_id, $customer->email);

           //processing payment on stripe
        //    $paymentIntent= new StripePaymentIntent();
        //    $paymentIntent->confirm($stripe_pi, $customer->stripe_customer_id);
           //
         
           $customer_id=$data->customer_id;

         
        //    if(!$customer_id){
        //     //create customer
        
        //         $customerModel= new CustomerModel($this->pdo);
        //         $customer_id=$customerModel->add([
        //             "firstname"=>$customer->firstname,
        //             "lastname"=> $customer->lastname,
        //             "stripe_customer_id"=> $data->customer_id]);

        //         }
          
          

           try {
            
            $this->pdo->beginTransaction();
            $signInOrder = new SignInOrderController($this->pdo);
            $order=$signInOrder->insertOrder($po_number, $stripe_pi,  $subtotal ,$total_price, $customer_id , $shopping_card);
         
         $orderEmail = new OrderEmailController($this->pdo);

           $event_id = $orderEmail->create($order['firstname'], $order['lastname'], $order['order']->po_number ,
                    $order["order"]->subtotal_price,  $order["order"]->total_price, 
                    $order['email_address'] ,   $order["order_detail_array"]);
               
                    
               
            if($event_id){
                $response['event_id']= $event_id;
            }

            //response['event_id']= event
            $this->pdo->commit();
            $response['success']= true;
            } catch (\Throwable $th) {
                $this->pdo->rollback();
                $response['success']= false;
            }
            
            echo json_encode($response);
    }
}


    public function getAllToDasboard()
    {
        $orderModel = new OrderModel($this->pdo);
        $orders= $orderModel->getAllInformationToAdmin();
        echo json_encode($orders);
    }

    public function getToDasboard($id)
    {
        $orderModel = new OrderModel($this->pdo);
        $order= $orderModel->getAllInformationToAdmin(true , $id);
        echo json_encode($order);
    }




    public function getTotalSumOrder()
    {
        $orderModel= new OrderModel($this->pdo);
        echo json_encode($orderModel->getTotalSumOrder());
    }





private function calculateOrderAmount( $items): int 
    {
    // Replace this constant with a calculation of the order's amount
    // Calculate the order total on the server to prevent
    // customers from directly manipulating the amount on the client
    $total=0.0;
    foreach($items as $item)
        {
            $total += floatval($item->product_price) * floatval($item->quantity);
        }
        return round(floatval(($total /100) * 100),2);
    }


   
}

