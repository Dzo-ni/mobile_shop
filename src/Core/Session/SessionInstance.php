<?php

// namespace App\Core\Session;
// use App\Core\Config;
// use App\Core\Session\SessionStorage;

// final class SessionInstance {
//     public function getSessionInstance(): \App\Core\Session\Session{
//         $fingerprintProviderFactoryClass = Config::FINGERPRINT_PROVIDER_FACTORY;
            
//         $fingerprintProviderFactoryMethod= Config::FINGERPRINT_PROVIDER_METHOD;
//         $fingerprintProviderFactoryArgs= Config::FINGERPRINT_PROVIDER_ARGS;
//         $fingerPrintProvideFactory = new  $fingerprintProviderFactoryClass();
//         // pp\\Core\\Fingerprint\\BasicFingerprintProviderFactory"  
    
    
//         $fingerprintProvider = $fingerPrintProvideFactory->$fingerprintProviderFactoryMethod(...$fingerprintProviderFactoryArgs);
    
    
    
//         $sessionStorageClassName = Config::SESSION_STORAGE;
//         $sessionStorageConstructorArguments = Config::SESSION_STORAGE_DATA;
//         $sessionStorage = new $sessionStorageClassName(...$sessionStorageConstructorArguments);
  
//         $session = new \App\Core\Session\Session($sessionStorage, Config::SESSION_LIFE_TIME ,$fingerprintProvider);
//         return $session;
//     }
 
// }