<?php
namespace App\CashingProviders;

use App\Core\Cache\CachingStorage;

class RedisCachingProvider implements CachingStorage{
    private $redis;
    public function __construct()
    {
        $this->redis = new \Predis\Client();
    }
    public function save(string $cacheId, string $cacheData){
        $this->predis->set($cacheId, $cacheData);
    }
    public function load(string $cacheId):string {
        return $this->predis->get($cacheId) ?? null;
    }
    public function delete(string $cacheId) {
        $this->predis->delete($cacheId);
    }
    public function cleanUp(int $cacheAge){
        //cleanUp
    }
}