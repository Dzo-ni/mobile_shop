<?php
namespace App\Core;



class Controller{
   public $pdo;
   private $session;
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
   
    final public function &getSession(): \App\Core\Session\Session{
       
        return $this->session;
    }
    
    final public function setSession(\App\Core\Session\Session &$session){
        
        $this->session = $session;
    }

}