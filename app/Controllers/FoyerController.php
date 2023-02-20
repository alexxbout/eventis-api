<?php

namespace App\Controllers;

class FoyerController extends BaseController {
    private $foyerModel;

    public function __construct() {
        $this->foyerModel = new \App\Models\FoyerModel();
    }

    public function getAll(): void {
        $this->send(200, $this->foyerModel->getAll());
    }

    public function getById(int $id): void {
        $this->send(200, $this->foyerModel->getById($id));
    }

    public function add(): void {
        $validation =  \Config\Services::validation();

        $validation->setRules([
            "city" => "required|max_length[10]",
            "zip" => "required|max_length[5]",
            "address" => "required|max_length[50]",
            "street" => "permit_empty|max_length[10]"
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $this->send(400, $validation->getErrors());
        } else {
            $data = $this->stdClassToArray($this->request->getJSON());

            $this->foyerModel->add($data);

            $this->send(200, ["message" => "Foyer added"]);
        }
    }

    public function updateData(): void {
        $validation =  \Config\Services::validation();

        $validation->setRules([
            "id" => "required|integer",
            "nom" => "permit_empty|max_length[30]",
            "prenom" => "permit_empty|max_length[30]",
            "login" => "permit_empty|max_length[30]"
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $this->send(400, $validation->getErrors());
        } else {
            $data = $this->stdClassToArray($this->request->getJSON());

            if (isset($data["password"])) {
                unset($data["password"]); // We don't want to update the password with this method
            }

            $this->foyerModel->updateData($data);

            $this->send(200, ["message" => "User updated", "data" => $data]);
        }
    }
}
