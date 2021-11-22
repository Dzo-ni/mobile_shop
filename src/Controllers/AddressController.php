<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\UserInformationTrait;
use App\Models\AddressModel;
use App\Models\CustomerModel;
use App\Models\EmailModel;
use App\Models\UserModel;

class AddressController extends Controller
{
    use UserInformationTrait;
   public function setAddress()
   {
       $data= file_get_contents("php://input");
       $user_id = $this->getSession()->get("user_id");
       $data=json_decode($data,true);
       
       if(!isset($data['firstname'],$data['lastname'],$data['street'],$data['city'])){
        die("All fields must be set");
       }
       $data['firstname'] = trim(filter_var($data['firstname']));
       $data['lastname'] = trim(filter_var($data['lastname']));
      
       $data['street'] = trim(filter_var($data['street']));
       $data['city'] = trim(filter_var($data['city']));
       foreach($data as $value){
           if(empty($value)){
               die("All value must be provided");
           }
       }
      
       
       $userModel= new UserModel($this->pdo);
       $customer_id = $userModel->getByFieldName("user_id", $user_id)->customer_id;
      
       $customer_data= [
           "firstname"=>$data['firstname'],
           "lastname"=>$data['lastname'],
       ];
      
       $customerModel = new CustomerModel($this->pdo);
       $customerModel->editById($customer_id, $customer_data);
      
      
       
        $addressModel = new AddressModel($this->pdo);
        $address_data = [
            "street"=>$data['street'],
            "city"=>$data['city'],
        ];
        $addressModel->updateOrInsert($customer_id, $address_data);
        $response=[];
        $user=$this->setUserInformationInSession($user_id);
        $response['status']=true;
        $response['user'] = $user;
        echo json_encode($response);
   }
}