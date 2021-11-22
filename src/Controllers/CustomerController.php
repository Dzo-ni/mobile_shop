<?php
namespace App\Controllers;

use App\Core\Controller;

class CustomerController extends Controller{
    //firsname lastname
    private $firstname;
    private $lastname;
    


 /**
     * Get the value of firstname
     */ 
    public function getFirstname()
    {
        return $this->firstname;
    }
    /**
     * Set the value of firstname
     *
     * @return  self
     */ 
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

    
    }

    /**
     * Set the value of lastname
     *
     * @return  self
     */ 
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

      
    }
    public function setCustomerStripeId($customer_stripe_id){
        $this->customer_stripe_id = $customer_stripe_id;
    }
    public function getCustomer(){
      
        return [
            "firstname"=>$this->firstname,
            "lastname"=>$this->lastname,
            "stripe_customer_id"=> $this->customer_stripe_id
        ];
    }

   
}