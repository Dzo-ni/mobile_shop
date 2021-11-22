<?php

namespace App\Sessions;
use App\Core\Session\SessionStorage;

class FileSessionStorage implements SessionStorage{
    private $sessionPath;
    public function __construct(string $sessionPath){
        $this->redis= new Predis\Client();
    }
    
    public function save(string $sessionId, string $sessionData){
        $this->redis->set($sessionId , $sessionData);
    }
    public function load(string $sessionId):string {
       return $this->redis->get($sessionId);
    }
    public function delete(string $sessionId){
        $this->redis->remove($sessionId);
        
    }
    public function cleanUp(int $sessionAge = 86400){

    }
}