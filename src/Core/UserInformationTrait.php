<?php
namespace App\Core;

use App\Models\UserModel;

trait UserInformationTrait
{
    protected function setUserInformationInSession($user_id){
        $userModel = new UserModel($this->pdo);
       
        $user= $userModel->getPersonalInformationForUser($user_id);
       
        $this->getSession()->put('login' , 1);
        $this->getSession()->put('user_id' ,   $user_id);
        $this->getSession()->save();
       
        return $user;
    }
}