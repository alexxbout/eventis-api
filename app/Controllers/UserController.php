<?php

namespace App\Controllers;

use App\Utils\HTTPCodes;
use App\Utils\Regex;

class UserController extends BaseController {
    private $userModel;

    public function __construct() {
        $this->userModel = new \App\Models\UserModel();
    }

    public function getAll(): void {
        $data = $this->userModel->getAll();
        if ($data != null) {
            $this->send(HTTPCodes::OK, $data);
        } else {
            $this->send(HTTPCodes::NO_CONTENT, []);
        }
    }

    public function getById(int $id): void {
        $data = $this->userModel->getById($id);
        if ($data != null) {
            $this->send(HTTPCodes::OK, $data);
        } else {
            $this->send(HTTPCodes::NOTFOUND, []);
        }
    }

    public function getByIdFoyer(int $idFoyer): void {
        $data = $this->userModel->getByIdFoyer($idFoyer);
        if ($data != null) {
            $this->send(HTTPCodes::OK, $data);
        } else {
            $this->send(HTTPCodes::NOTFOUND, []);
        }
    }

    public function getByIdRole(int $idRole): void {
        $data = $this->userModel->getByIdRole($idRole);
        if ($data != null) {
            $this->send(HTTPCodes::OK, $data);
        } else {
            $this->send(HTTPCodes::NOTFOUND, []);
        }
    }

    public function getByIdRef(int $idRef): void {
        $data = $this->userModel->getByIdRef($idRef);
        if ($data != null) {
            $this->send(HTTPCodes::OK, $data);
        } else {
            $this->send(HTTPCodes::NOTFOUND, []);
        }
    }

    public function add(): void {
        $validation =  \Config\Services::validation();

        $validation->setRules([
            "nom" => "required|max_length[30]",
            "prenom" => "required|max_length[30]",
            "login" => "required|max_length[30]",
            "password" => "required|regex_match[" . Regex::PASSWORD ."]",
            "idFoyer" => "required|integer",
            "idRole" => "required|integer"
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $this->send(HTTPCodes::BAD_REQUEST, $validation->getErrors());
        } else {
            $data = $this->request->getJSON(true);

            $data["password"] = $this->encodePassword($data["password"]);

            $id = $this->userModel->add($data);

            $this->send(HTTPCodes::OK, ["message" => "User added", "id" => $id, "data" => $data]);
        }
    }

    public function updateLastLogin(): void {
        $validation =  \Config\Services::validation();

        $validation->setRules([
            "id" => "required|integer"
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $this->send(HTTPCodes::BAD_REQUEST, $validation->getErrors());
        } else {
            $data = $this->request->getJSON(true);

            $this->userModel->updateLastLogin($data["id"]);

            $this->send(HTTPCodes::OK, ["message" => "Last login updated"]);
        }
    }

    public function updateLastLogout(): void {
        $validation =  \Config\Services::validation();

        $validation->setRules([
            "id" => "required|integer"
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $this->send(HTTPCodes::BAD_REQUEST, $validation->getErrors());
        } else {
            $data = $this->request->getJSON(true);

            $this->userModel->updateLastLogout($data["id"]);

            $this->send(HTTPCodes::OK, ["message" => "Last logout updated"]);
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
            $this->send(HTTPCodes::BAD_REQUEST, $validation->getErrors());
        } else {
            $data = $this->request->getJSON(true);

            if (isset($data["password"])) {
                unset($data["password"]); // We don't want to update the password with this method
            }

            $this->userModel->updateData($data);

            $this->send(HTTPCodes::OK, ["message" => "User updated", "data" => $data]);
        }
    }

    private function encodePassword(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
