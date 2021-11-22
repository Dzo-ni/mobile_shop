<?php

namespace App\Core\Fingerprint;
 
class BasicFingerprintProviderFactory {
    public function getInstance(string $arraySource): BasicFingerprintProvider{
        switch($arraySource){
            case "SERVER":
                return new \App\Core\Fingerprint\BasicFingerprintProvider($_SERVER);
      
            break;

            default:
                 return new \App\Core\Fingerprint\BasicFingerprintProvider($_SERVER);
    }
}

}