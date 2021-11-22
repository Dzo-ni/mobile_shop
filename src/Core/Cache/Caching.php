<?php
namespace App\Core\Cache;
class Caching{
    private  $cachingStorage;
    private array $cacheData;
    private  $cachingLifeTime;
    public function __construct($cachingStorage)
    {
        $this->cachingStorage = $cachingStorage;
    }

    public function getCashingStorage(){
        return  $this->cachingStorage; 
    }

}