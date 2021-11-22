<?php

namespace App\Controllers;

use Dotenv\Dotenv;
use App\Core\Controller;
use App\Models\OrderModel;

class WebhookController extends Controller{

    public function stripe(){
      file_put_contents("proba.txt","test");
      die;
      $dotenv = Dotenv::createImmutable(dirname(dirname(dirname(__DIR__))));
      $dotenv->load();
      $sk_key = $_ENV['STRIPE_SK'];
      $stripe_details = [
          "pk_key" => "pk_test_oEHQgWKWhkD12GWBTKgIX8qH",
          "sk_key"=> $sk_key
      ];
      \Stripe\Stripe::setMaxNetworkRetries(2);
      \Stripe\Stripe::setApiKey($stripe_details["sk_key"]);
  
ob_start();


        $orderModel = new OrderModel($this->pdo);
        $payload = @file_get_contents('php://input');
        $event = null;
        try {
          $event = \Stripe\Event::constructFrom(
            json_decode($payload, true)
          );
        } catch(\UnexpectedValueException $e) {
          // Invalid payload
          echo '⚠️  Webhook error while parsing basic request.';
          http_response_code(400);
         
          exit();
        }
     
        // Handle the event
        $paymentIntent_id= $event->data->object->id;
        switch ($event->type) {
          case 'payment_intent.succeeded':
            
           //when payment succeeded then 
            //update column sales.status from reserved to successfull
           $orderModel->updateStatus(true, $paymentIntent_id);
            break;
            case 'payment_intent.payment_failed':
              
            //when payment failed then 
            //update column sales.status from reserved to failed
            $orderModel->updateStatus(false, $paymentIntent_id);
            //update product.product_quantity to  set product.product_quantity = product.product_quantity + product.sales_quantity
            $orderModel->returnToStock( $paymentIntent_id);
              break;
          default:
            // Unexpected event type
            echo 'Received unknown event type';
        }
        echo $event->data->object->id;
         error_log(ob_get_clean(),4);
       
   
    }
}