<?php
namespace App\Core\Cache;

interface CachingStorage{
    public function save(string $cacheId, string $cacheData);
    public function load(string $cacheId):string;
    public function delete(string $cacheId);
    public function cleanUp(int $cacheAge);
}