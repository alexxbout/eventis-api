<?php

namespace App\Controllers;

class UserController extends BaseController {
    private $userModel;

    public function __construct() {
        $this->userModel = new \App\Models\UserModel();
    }

    public function getAll(): void {
        $this->send(200, $this->userModel->getAll());
    }

    public function getById(int $id): void {
        $this->send(200, $this->userModel->getById($id));
    }

    public function getByIdFoyer(int $idFoyer): void {
        $this->send(200, $this->userModel->getByIdFoyer($idFoyer));
    }

    public function getByIdRole(int $idRole): void {
        $this->send(200, $this->userModel->getByIdRole($idRole));
    }

    public function getByIdRef(int $idRef): void {
        $this->send(200, $this->userModel->getByIdRef($idRef));
    }

    public function add(): void {
        $validation =  \Config\Services::validation();

        $validation->setRules([
            "nom" => "required|max_length[30]",
            "prenom" => "required|max_length[30]",
            "login" => "required|max_length[30]",
            "password" => "required|regex_match[/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^\w\d\s:])([^\s]){8,100}$/]",
            "idFoyer" => "required|integer",
            "idRole" => "required|integer"
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $this->send(400, $validation->getErrors());
        } else {
            $data = $this->stdClassToArray($this->request->getJSON());

            $data["password"] = $this->encodePassword($data["password"]);

            $id = $this->userModel->add($data);

            $this->send(200, ["message" => "User added", "id" => $id, "data" => $data]);
        }
    }

    public function updateLastLogin(): void {
        $validation =  \Config\Services::validation();

        $validation->setRules([
            "id" => "required|integer"
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $this->send(400, $validation->getErrors());
        } else {
            $data = $this->stdClassToArray($this->request->getJSON());

            $this->userModel->updateLastLogin($data["id"]);

            $this->send(200, ["message" => "Last login updated"]);
        }
    }

    public function updateLastLogout(): void {
        $validation =  \Config\Services::validation();

        $validation->setRules([
            "id" => "required|integer"
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $this->send(400, $validation->getErrors());
        } else {
            $data = $this->stdClassToArray($this->request->getJSON());

            $this->userModel->updateLastLogout($data["id"]);

            $this->send(200, ["message" => "Last logout updated"]);
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

            $this->userModel->updateData($data);

            $this->send(200, ["message" => "User updated", "data" => $data]);
        }
    }

    private function encodePassword(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
