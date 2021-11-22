<?php
namespace App\Controllers;


use App\Core\Config;
use App\Core\Controller;
use App\Core\DBConnection;
use App\Models\ProductModel;


class FilterController extends Controller{

    
 
    public function getAll(){
        $cacheStorageClassName = Config::CACHE_STORAGE;
        $cacheStorageConstructorArguments = Config::CACHE_STORAGE_DATA;
        $cacheStorage = new $cacheStorageClassName(...$cacheStorageConstructorArguments);
    
        $cache = new \App\Core\Cache\Caching($cacheStorage, Config::CACHE_LIFE_TIME);
        $instanceOfCache= $cache->getCashingStorage();
       //if cache exist and still relevant get filters from cache
        if($instanceOfCache->exists("filters")) {
            echo $instanceOfCache->load("filters");
            exit;
        }

        $productModel = new ProductModel($this->pdo);
        $result['brands']= $productModel->getAllByProductStatus("product_brand");
        // $query = "select distinct(product_brand) from product where product_status = '1' order by product_brand desc";
      
        $result['rams']= $productModel->getAllByProductStatus("product_ram");
        $query = "select distinct(product_ram) from product where product_status = '1' order by product_ram desc";
     
       
        $result['storages']= $productModel->getAllByProductStatus("product_storage");
        // $query = "select distinct(product_storage) from product where product_status = '1' order by product_storage desc";
   
        $instanceOfCache->save("filters" , json_encode($result));
        //save in cache for second time;
        echo json_encode($result);
    }



   
}