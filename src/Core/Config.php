<?php

namespace App\Core;

use Dotenv\Dotenv;

final class Config
{
    
    const USER_STATUS_REGISTER = 1;
    const USER_STATUS_ACTIVATED = 2;

    const USER_ROLE_USER = 1;
    const USER_ROLE_ADMINISTRATOR = 2;

    const EMAIL_NOT_VERIFIED = 1;
    const EMAIL_VERIFIED = 2;
   
    const CUSTOMER_NOT_REGISTERED = 0;
    const CUSTOMER_REGISTERED = 1;
    

    const PASSWORD_MINIMUM_LENGTH=1;
    const PASSWORD_MINIMUM_REQUIREMENTS=2;

    const ORDER_RESERVED = 1;
    const ORDER_FAILED = 2;
    const ORDER_CONFIRMED = 3;

    const SESSION_STORAGE = '\\App\\Sessions\\FileSessionStorage';
    const SESSION_STORAGE_DATA = ['./../runtime/sessions/'];
    const SESSION_LIFE_TIME = 3600;
    // const SESSION_STORAGE = '\\App\\Sessions\\RedisSessionStorage';
    // const SESSION_STORAGE_DATA = ['./../sessions/'];
    // const SESSION_LIFE_TIME = 3600;

    const CACHE_STORAGE = '\\App\\CachingProviders\\FileCachingProvider';
    const CACHE_STORAGE_DATA = ['./../runtime/cache/'];
    const CACHE_LIFE_TIME = 3600;
  
    


    const FINGERPRINT_PROVIDER_FACTORY = "\\App\\Core\\Fingerprint\\BasicFingerprintProviderFactory";
    const FINGERPRINT_PROVIDER_METHOD ='getInstance';
    const FINGERPRINT_PROVIDER_ARGS = ['SERVER'];
    

   
     
}
