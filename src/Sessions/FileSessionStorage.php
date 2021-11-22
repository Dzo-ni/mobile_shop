<?php

namespace App\Sessions;
use App\Core\Session\SessionStorage;

class FileSessionStorage implements SessionStorage{
    private $sessionPath;
    public function __construct(string $sessionPath){
        $this->sessionPath= $sessionPath;
    }
    
    public function save(string $sessionId, string $sessionData){
        $sessionFileName = $this->sessionPath . $sessionId . ".json";
        
        file_put_contents($sessionFileName, $sessionData);
    }
    public function load(string $sessionId):string {
        $sessionFileName = $this->sessionPath . $sessionId . ".json";
      
        if(!file_exists($sessionFileName)){
            return "{}";
        }
        return file_get_contents($sessionFileName);
    }
    public function delete(string $sessionId){
        $sessionFileName = $this->sessionPath . $sessionId . ".json";
        if(file_exists($sessionFileName)){
           unlink( $sessionFileName);
        }
        
    }
    public function cleanUp(int $sessionAge = 86400){
        $allFiles = scandir($this->sessionPath); // Or any other directory
        $files = array_diff($allFiles, array('.', '..'));
    
        foreach($files as $file){
           if(\time() - \filemtime($this->sessionPath . $file) > $sessionAge){
                 echo unlink($this->sessionPath . $file);
           }
        }
        //obrisati datoteke starije od npr 7 dana
        // ako je datum poslednje izmene veca od $sessionAge file obrisati
    }
}