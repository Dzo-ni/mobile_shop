<?php
namespace App\Models;

class StripeUserModel{
    private $pdo;
    private $customer_id;
    private $user_id;
    public function __construct(&$pdo)
    {
        $this->pdo = $pdo;
    }
    

    public function register(){
       
        $query ="INSERT INTO stripe_users VALUES(null, ? , ?)";
       
        $stmt = $this->pdo->prepare($query);
        
       return $stmt->execute([$this->customer_id,$this->user_id]);
    }
    public function getCustomerId($field , $type){
     
        if($type == 'user_id'){
            $query ="SELECT customer_id FROM stripe_users WHERE user_id = ?";
        }elseif ($type == 'email') {
            $query ="SELECT customer_id FROM stripe_users WHERE email = ?";
        }
     
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$field]);
        $stripeCustomerID = $stmt->fetchColumn();
        return $stripeCustomerID;
    }

    /**
     * Get the value of user_id
     */ 
    public function getUser_id()
    {
        return $this->user_id;
    }

    /**
     * Set the value of user_id
     *
     * @return  self
     */ 
    public function setUser_id($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * Get the value of customer_id
     */ 
    public function getCustomer_id()
    {
        return $this->customer_id;
    }

    /**
     * Set the value of customer_id
     *
     * @return  self
     */ 
    public function setCustomer_id($customer_id)
    {
        $this->customer_id = $customer_id;

        return $this;
    }
}