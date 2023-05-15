<?php

namespace App\Controllers;

use App\Utils\HTTPCodes;

class RoleController extends BaseController {
    private $roleModel;
    
    
    public function __construct() {
        $this->roleModel = new \App\Models\RoleModel();
    }
    
    public function getAll(): void {
        if ($this->user->isDeveloper()) {
            $exist =  $this->roleModel->getAll();
            if (sizeof($exist)==0){ $this->send(HTTPCodes::NOT_FOUND);}
            else { $this->send(200, $exist); }
        }else{
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }
}