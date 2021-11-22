<?php
namespace App\Controllers;

use App\Core\Controller;

use App\Models\StockModel;

class DashboardController extends Controller{
    public function getStatisticalDataProducts(){
        $stockModel =new StockModel($this->pdo);
        echo json_encode($stockModel->getAllProductsNameAndQuantity());
    }
}