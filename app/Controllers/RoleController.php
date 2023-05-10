<?php

namespace App\Controllers;

use App\Utils\HTTPCodes;

use function PHPUnit\Framework\isEmpty;

class RoleController extends BaseController {
    private $roleModel;

    public function __construct() {
        $this->roleModel = new \App\Models\FoyerModel();
    }

    public function getAll(): void {
        $tab = $this->roleModel->getAll();
        if($tab == NULL){
            $this->send(HTTPCodes::NOT_FOUND,$tab);
        }
        else if($tab.isEmpty()){
            $this->send(HTTPCodes::NO_CONTENT,$tab);
        }
        else{
            $this->send(HTTPCodes::OK,$tab);
        }  
    }

    public function getById(int $id): void {
        $this->send(200, $this->roleModel->getById($id));
    }

    public function getByLibelle(String $libelle): void {
        $this->send(200, $this->roleModel->getByLibelle($libelle));
    }

}
