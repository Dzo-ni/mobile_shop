<?php

namespace App\Core\Session;
use App\Core\Session\SessionStorage;

final class Session {
    private  $sessionStorage;
    private array $sessionData;
    public $sessionId;
    private $sessionLife;
    private $fingerprintProvider;
    public function __construct(SessionStorage $sessionStorage , int $sessionLife = 1800,$fingerprintProvider = null)
    {
        $this->sessionStorage = $sessionStorage;
        $this->sessionLife= $sessionLife;
        $this->sessionData=[];
        $this->fingerprintProvider = $fingerprintProvider;
        
        $this->sessionId = \filter_input(INPUT_COOKIE,'APPSESSION',FILTER_SANITIZE_STRING) ;
        $this->sessionId = \preg_replace("|[^A-Za-z0-9]|", "" , $this->sessionId ) ;
       
        if(strlen($this->sessionId) !== 32) {
            $this->regenerate();
        }
    
    }
    //agains
//    private function settoken(){
//        $this->put("_token", uniqid(rand(),true));
//    }
    public function setFingerprintProvider(\App\Core\Fingerprint\FingerprintProvider $fp){
        $this->fingerprintProvider = $fp;
    }

    public function put(string $key , $value){
        $this->sessionData[$key]=$value;
    }

    public function get(string $key , $defaultValue= null){
        return $this->sessionData[$key] ?? $defaultValue;
    }

    public function clear(){
        $this->sessionData = [];
    }

    public function exists(string $key) : bool{
        return isset($this->sessionData[$key]);
    }
    public function has(string $key) : bool{
        return isset($this->sessionData[$key]) &&  
                            boolval($this->sessionData[$key]);
    }

    public function save(){
        if(!$this->fingerprintProvider ){
            echo "fingerprint is not valid";
            die;
        }
        $fingerprint= $this->fingerprintProvider->provideFingerprint();
        
       
        $this->sessionData["__fingerprint"]= $fingerprint;
        // $this->settoken();
        $this->sessionStorage->save($this->sessionId , \json_encode($this->sessionData));
        $this->setCookie();
    }

    public function reload(){
       
        $jsonData=$this->sessionStorage->load($this->sessionId);
        $restodedData = json_decode( $jsonData,true);
        if(!$restodedData){
        
            $this->sessionData = [];
            return;
        }
        $this->sessionData = $restodedData;
        
        //if fingerprintProvider doesnt set
        if(!$this->fingerprintProvider){
            return;
        }

        $savedFingerprint = $this->sessionData['__fingerprint'] ?? null;
         //if fingerprintProvider previosly havent been set
        if(!$savedFingerprint){
            return;
        }
       
        $currentFingerprint = $this->fingerprintProvider->provideFingerprint();
        if( $currentFingerprint !== $savedFingerprint){
            $this->clear();
            $this->sessionStorage->delete($this->sessionId);
            $this->sessionId = $this->generateSessionId();
            $this->save();
        }
    }

    public function regenerate(){
        $this->reload();
        $this->sessionStorage->delete($this->sessionId);
        $this->sessionId = $this->generateSessionId();
        $this->save();
    }

    private function generateSessionId():string
    {
     $supported = 'QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm1234567890';
     $id="";
     for ($i=0; $i < 32; $i++) { 
        $id .= $supported[rand(0,strlen($supported)-1)];
     }
     return $id;
    }

    private function setCookie(){
        setcookie("APPSESSION", $this->sessionId,time() + $this->sessionLife , "/");
    }

   
}