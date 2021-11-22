<?php
namespace App\Controllers;

use App\Core\DBConnection;
use App\Models\OrderModel;

class MainController{
    public function home(){
        file_put_contents("proba.txt","pocetna");
        echo "pocetna";
    }
    
    public function delete(){
        echo "ovde";
    }
}