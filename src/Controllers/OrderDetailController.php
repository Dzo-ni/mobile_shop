<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\OrderDetailModel;

class OrderDetailController extends Controller{


public function getToDasboard($id){
    $orderDetailModel = new OrderDetailModel($this->pdo);
    $order= $orderDetailModel->getInformation($id);
    echo json_encode($order);
}



}