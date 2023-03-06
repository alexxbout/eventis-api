<?php

namespace App\Controllers;

class RoleController extends BaseController {
    private $roleModel;

    public function __construct() {
        $this->roleModel = new \App\Models\FoyerModel();
    }

    public function getAll(): void {
        $this->send(200, $this->roleModel->getAll());
    }

    public function getById(int $id): void {
        $this->send(200, $this->roleModel->getById($id));
    }

    public function getByLibelle(String $libelle): void {
        $this->send(200, $this->roleModel->getByLibelle($libelle));
    }

}
