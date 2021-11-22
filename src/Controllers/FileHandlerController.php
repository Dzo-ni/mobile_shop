<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\FileHandler;
use App\Sessions\FileSessionStorage;

class FileHandlerController extends Controller implements FileHandler {
    public function delete(){
        $sessionPath= __DIR__."../../../sessions/";
        $fsSession = new FileSessionStorage($sessionPath);
        $fsSession->cleanUp();
    }
}