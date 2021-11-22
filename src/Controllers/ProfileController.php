<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;
use App\Models\OrderModel;
use App\Core\Session\SessionInstance;
use App\Models\OrderDetailModel;

class ProfileController extends Controller{

    public function personalInformation($id){
        //get all infromation
        //firstname,lastname, email_address, address, city , zip , telephone
    }

    public function show($id){
        $orderModel= new OrderModel($this->pdo);
        // echo json_encode($order);
        $orderDetailModel= new OrderDetailModel($this->pdo);
        // echo json_encode($orderDetailModel->getAllByFieldName("order_id",$id));
        $response['order_details'] = $orderDetailModel->getInformation($id);
        $response['order'] = $orderModel->getById($id);
        echo json_encode($response);
        
    }
    public function orders(){
        // if($this->getSession()->has("user_id")){}
        
        $userId = $this->getSession()->get("user_id");

        $userModel = new UserModel($this->pdo);
        $customer_id=$userModel->getCustomerIdById($userId);
        $orderModel= new OrderModel($this->pdo);
      
        $ordersHistory= $orderModel->getAllByFieldName("customer_id" , $customer_id);
        $ordersList=[];
        $jsonArr="[";
        $whiteListKeys= ["order_id","total_price","created_at","po_number"];
        foreach ($ordersHistory as $order => $order_value) {
            $jsonArr.="{";
            foreach ($order_value as $key => $value) {
              
                if(in_array($key,$whiteListKeys)){
                    $jsonArr.="\"".$key . "\" : \"" .$value. "\",";
                }
            }
            $jsonArr= trim($jsonArr,",");
            $jsonArr.= "},";
        }
        $jsonArr= trim($jsonArr,",");
        $jsonArr .= "]";
        die(($jsonArr));
    }
}