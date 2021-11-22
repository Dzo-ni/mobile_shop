<?php

namespace App\Controllers\Payment;

use Dotenv\Dotenv;
use App\Core\Controller;
use Stripe\PaymentMethod;

class StripePaymentIntent{
    private $paymentIntent;

    // public function confirm($payment_intent, $payment_method=false){
    //     $dotenv = Dotenv::createImmutable(dirname(dirname(dirname(__DIR__))));
    //     $dotenv->load();
    //     $sk_key = $_ENV['STRIPE_SK'];
    //     $stripe_details = [
    //         "pk_key" => "pk_test_oEHQgWKWhkD12GWBTKgIX8qH",
    //         "sk_key"=> $sk_key
    //     ];
       
    //     if(!$payment_method){
    //         $stripe = new \Stripe\StripeClient(
    //             $stripe_details["sk_key"]
    //           );
    //        return $stripe->paymentIntents->confirm($payment_intent,['setup_future_usage' => 'off_session']);
    //     }else{
    //         // \Stripe\PaymentIntent::confirm($payment_intent,
    //         // ['payment_method' => 'pm_card_visa'])
    //     }
       
    // }
    public function create($customer_id, $items){
      
        $dotenv = Dotenv::createImmutable(dirname(dirname(dirname(__DIR__))));
        $dotenv->load();
        $sk_key = $_ENV['STRIPE_SK'];
        $stripe_details = [
            "pk_key" => "pk_test_oEHQgWKWhkD12GWBTKgIX8qH",
            "sk_key"=> $sk_key
        ];
        \Stripe\Stripe::setMaxNetworkRetries(2);
        \Stripe\Stripe::setApiKey($stripe_details["sk_key"]);
        $this->paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => PaymentCalculator::calculateOrderAmount($items),
            'currency' => 'usd',
            'customer'  =>$customer_id,
            'setup_future_usage' => 'off_session',
            ]);
          
            return $this->paymentIntent;
    }



    public function update($paymentIntent_id,$customer_id,$items){
      
        $dotenv = Dotenv::createImmutable(dirname(dirname(dirname(__DIR__))));
        $dotenv->load();
        $sk_key = $_ENV['STRIPE_SK'];
     
        $stripe_details = [
            "pk_key" => "pk_test_oEHQgWKWhkD12GWBTKgIX8qH",
            "sk_key"=> $sk_key
        ];
        \Stripe\Stripe::setMaxNetworkRetries(2);
        \Stripe\Stripe::setApiKey($stripe_details["sk_key"]);
      
        $this->paymentIntent =  \Stripe\PaymentIntent::update(
            $paymentIntent_id ,['amount' => PaymentCalculator::calculateOrderAmount($items),'customer'=>$customer_id]
          );
       
          return $this->paymentIntent;
    }
}