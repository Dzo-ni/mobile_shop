<?php

namespace App\Core;

class Request{
    public $is_api_route=false;
  
    public function getPath(){
        $path= $_SERVER['REQUEST_URI'] ?? "/";
       
        $position=  strpos($path, "?");
      
        if(!$position) {
          $path= $this->sanitizeLastSlash($path);
         
          if($this->checkIsApiRoute($path)){
              //set true on is_api_route;
              $this->is_api_route =true;
          }
          return $path;
        };

  //if in uri there query string just cut them 
        $path= substr($path,0,$position);
        $path= $this->sanitizeLastSlash($path);
        if($this->checkIsApiRoute($path)){
            //set true on is_api_route;
            $this->is_api_route =true;
        }
        return $path;
    }
    public function getMethod(){
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    //return true if it is api route
    private function checkIsApiRoute($path){
        //check if route start with /api/
        if(strpos(substr($path,0,5) ,"/api/") !==false) {
            return true;
        }
        return false;
    }

    private function sanitizeLastSlash($path){
        //basically check if forward slash exist on end of the path
        //and first of all is not route: /
        $len = strlen($path);
        if($len>1 && $path[ $len-1] == "/"){
            return substr($path,0, $len-1);
        }
        return $path;
    }
}