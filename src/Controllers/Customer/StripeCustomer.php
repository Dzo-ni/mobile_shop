<?php

namespace App\Controllers\Customer;

use Dotenv\Dotenv;
use App\Core\Controller;
use App\Models\CustomerModel;


class StripeCustomer extends Controller{
    private CustomerModel $customerModel;
    public function __construct($db)
    {
        $this->customerModel= new CustomerModel($db);
    }
    public function getIdByUserId($userId){
   
        return $this->customerModel->getStripeCustomerId($userId, 'user_id');
    }

    public function getIdByUserEmail($userEmail){
     
        return $this->customerModel->getStripeCustomerId($userEmail,'email_address');
    }
public function create($uuid , $email_address){
    
    $dotenv = Dotenv::createImmutable(dirname(dirname(dirname(__DIR__))));
    $dotenv->load();
    $sk_key = $_ENV['STRIPE_SK'];
    $stripe_details = [
        "pk_key" => "pk_test_oEHQgWKWhkD12GWBTKgIX8qH",
        "sk_key"=> $sk_key
    ];
    \Stripe\Stripe::setMaxNetworkRetries(2);
    \Stripe\Stripe::setApiKey($stripe_details["sk_key"]);

    $stripe_customer= \Stripe\Customer::create([
        'description' =>$email_address,
    ]
    , [
        'idempotency_key' => $uuid
      ]);
    return $stripe_customer->id;
}
    
    
}