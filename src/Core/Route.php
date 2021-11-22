<?php
namespace App\Core;
final class Route{
    private $requestMethod = 'ANY';
    private $controller;
    private $method;
    private $pattern;
    private function __construct(string $pattern, string $requestMethod, string $controller,string $method)
    {
        $this->pattern = $pattern;
        $this->requestMethod = $requestMethod;
        $this->controller = $controller;
        $this->method = $method;
    }

    public static function get(string $pattern, string $controller, string $method){
        return new Route($pattern, 'GET',$controller,$method);
    }
    public static function post(string $pattern, string $controller, string $method){
        return new Route($pattern, 'POST',$controller,$method);
    }
    public static function any(string $pattern, string $controller, string $method){
        return new Route($pattern, 'GET|POST',$controller,$method);
    }

    public function matches(string $method , string $url) :bool {
        //check request method
        if(!preg_match('/^'.$this->requestMethod.'$/',$method)){
            return false;
        }
        //if request method matches, then check and url
        return boolval(preg_match($this->pattern,$url));
        //if return true route is valid
    }

    public function getControllerName():string{
        return $this->controller;
    }

    public function getMethodName():string{
        return $this->method;
    }

    public function extractArguments(string $url):array {
        $arguments=[];
       
        preg_match_all($this->pattern, $url, $matches);
       
        if(isset($matches[1])){
            $arguments[] = $matches[1][0];
        }
        if(isset($matches[2])){
            $arguments[] = $matches[2][0];
        }
  
        return $arguments;
    }
}