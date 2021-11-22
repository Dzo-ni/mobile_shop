<?php
namespace App\CachingProviders;
use App\Core\Cache\CachingStorage;

class FileCachingProvider implements CachingStorage{
    //runtime/cache/....
     private $cachingPath;
     
        public function __construct(string $cachingPath){
            $this->cachingPath= $cachingPath;
        }
        
        public function save(string $cacheId, string $cacheData){
            $cacheFileName = $this->cachingPath . $cacheId . ".json";
            file_put_contents($cacheFileName, $cacheData);
        }
        public function load(string $cacheId):string {
            $cacheFileName = $this->cachingPath . $cacheId . ".json";
          
            if(!file_exists($cacheFileName)){
                return "{}";
            }
            return file_get_contents($cacheFileName);
        }
        public function delete(string $cacheId){
            $cacheFileName = $this->cachingPath . $cacheId . ".json";
            if(file_exists($cacheFileName)){
               unlink( $cacheFileName);
            }
        }
        public function exists(string $cacheId){
            $cacheFileName = $this->cachingPath . $cacheId . ".json";
            return \boolval(\file_exists($cacheFileName));
        }
        public function cleanUp(int $sessionAge = 86400){
            $allFiles = scandir(__DIR__ . "/../../runtime/cache/"); // Or any other directory
            $files = array_diff($allFiles, array('.', '..'));
            var_dump(json_encode($files));
            foreach($files as $file){
               if(time() - filemtime($file) > $sessionAge){
                unlink($file);
               }
            }
            //obrisati datoteke starije od npr 7 dana
            // ako je datum poslednje izmene veca od $sessionAge file obrisati
        }
    }
