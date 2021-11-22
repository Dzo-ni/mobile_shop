<?php
namespace App\Response;

class ResponseMessage{
     public static function emailInvalid(){
        $response['status'] = 0;
        $response['msg'] = 'Email invalid';
        return $response;
    }
    
    public static function emailExistInDatabase(){
        $response['status'] = 0;
        $response['msg'] = 'Email already exist';
        return $response;
    }

    public static function passwordsDoesntMatch(){
        $response['status'] = 0;
        $response['msg'] = "Password and repassword must match";
        return $response;
    }

    public static function passwordMinimumLength(){
        $response['status'] = 0;
        $response['msg'] = 'Password must be minimum 8 characters';
        return $response;
    }
     public static function passwordMinimumRequirements(){
        $response['status'] = 0;
        $response['msg'] = 'Password must contain minimum one capital letter and minimum one digit';
        return $response;
    }

    public static function registeredSuccess(){
        $response['status'] =1;
        $response['msg'] = 'Uspesno ste se registrovali.Molimo Vas proverite email i kliknite na aktivacioni link';
        return $response;
    }
    
    
}