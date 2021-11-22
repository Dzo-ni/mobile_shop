<?php

namespace App\Controllers\Order;

use App\Core\Config;
use App\Core\Controller;
use App\Models\EmailModel;
use App\Models\CustomerModel;
use App\Models\OrderDetailModel;
use App\Models\OrderModel;

class SignInOrderController extends Controller
{
    public function insertOrder($po_number, $stripe_pi,  $subtotal ,$total_price, $customer_id , $shopping_card){
        $orderModel = new OrderModel($this->pdo);
        $order_id= $orderModel->insertOrder($po_number, $stripe_pi,  $subtotal ,$total_price, Config::ORDER_RESERVED, $customer_id);
        
        $orderDetailModel= new OrderDetailModel($this->pdo);
        $orderDetailModel->insertOrderDetail($shopping_card,$order_id);
        //get customer firstname and lastname and address and email_address
        $customerModel=new CustomerModel($this->pdo);
        $customer=$customerModel->getById($customer_id);
        $orderDetailArray= $orderDetailModel->getInformation($order_id);
        $emailModel = new EmailModel($this->pdo);
        $email= $emailModel->getByFieldName("customer_id",$customer_id);

        $firstname = $customer->firstname;
        $lastname = $customer->lastname;
        $email_address = $email->email_address;
        //company information
        //get html table with product_name and quntity

        //get order subtotal_price, taxes price and total_price 
        $order = $orderModel->getById($order_id);
        return [
            "firstname"=> $firstname , 
            "lastname"=> $lastname , 
            "email_address"=> $email_address , 
            "order_detail_array"=>  $orderDetailArray,
            "order"=>$order
        ];
    }
}