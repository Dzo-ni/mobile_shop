<?php
require_once __DIR__."/../vendor/autoload.php";
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");

use Dotenv\Dotenv;
use App\Core\Config;

use App\Core\Router;
use App\Core\Application;
use App\Core\DBConnection;
use App\Core\Session\Session;
use App\Controllers\SiteController;
use App\Controllers\FilterController;
use App\Core\Session\SessionInstance;
// Parameters passed using a named array:

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->load();
       
       
$url = filter_input(INPUT_GET, 'URL') ?? "/";
$httpMethod =filter_input(INPUT_SERVER,"REQUEST_METHOD");


$router = new Router();
$routes=include_once __DIR__."/../routes.php";
foreach($routes as $route){
    $router->add($route);
}


$route=$router->find($httpMethod,$url);
$arguments=$route->extractArguments($url);

// $app = new Application(dirname(__DIR__));



$fullContollerName = "\\App\\Controllers\\".$route->getControllerName().'Controller';

$db = new DBConnection();
$pdo =  $db->getConnection();
$controller = new $fullContollerName($pdo);

$fingerprintProviderFactoryClass = Config::FINGERPRINT_PROVIDER_FACTORY;
   
$fingerprintProviderFactoryMethod= Config::FINGERPRINT_PROVIDER_METHOD;
$fingerprintProviderFactoryArgs= Config::FINGERPRINT_PROVIDER_ARGS;
$fingerPrintProvideFactory = new  $fingerprintProviderFactoryClass();
// pp\\Core\\Fingerprint\\BasicFingerprintProviderFactory"


$fingerprintProvider = $fingerPrintProvideFactory->$fingerprintProviderFactoryMethod(...$fingerprintProviderFactoryArgs);


$sessionStorageClassName = Config::SESSION_STORAGE;
$sessionStorageConstructorArguments = Config::SESSION_STORAGE_DATA;
$sessionStorage = new $sessionStorageClassName(...$sessionStorageConstructorArguments);

$session = new \App\Core\Session\Session($sessionStorage, Config::SESSION_LIFE_TIME ,$fingerprintProvider );

$controller->setSession($session);

$controller->getSession()->reload();
          
            
$methodName= $route->getMethodName();

        

    

call_user_func_array([$controller ,$methodName],$arguments);



//route


// //api route
// $app->router->get("/api/filters/{:id}",[FilterController::class,'filters']);






// //html route
// $app->router->get("/",[SiteController::class,'home']);
// $app->router->get("/contact",[SiteController::class,'contact']);
// $app->router->post("/contact",[SiteController::class,'handleContact']);



// $app->run();
