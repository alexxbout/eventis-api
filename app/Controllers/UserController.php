<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Utils\HTTPCodes;
use Psr\Log\LoggerInterface;
use App\Utils\Regex;

class UserController extends BaseController {
    private $userModel;

    // Pas de new xxxController
    // Pour appeler le constructeur de la classe, on appel initController(...) et on oublie pas d'appeler la méthode parent
    //
    // Changer la méthode suivante par public function initController(...)
    // public function __construct() {
    //     $this->userModel = new \App\Models\UserModel();
    // }
    //
    // Et ne pas oublier d'importer ces trois lignes :
    // use CodeIgniter\HTTP\RequestInterface;
    // use CodeIgniter\HTTP\ResponseInterface;
    // use Psr\Log\LoggerInterface;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        parent::initController($request, $response, $logger);
        $this->userModel = new \App\Models\UserModel();
    }

    public function getAll(): void {
        $data = $this->userModel->getAll();
        if ($data != null) {
            $this->send(HTTPCodes::OK, $data, "OK");
        } else {
            $this->send(HTTPCodes::NO_CONTENT);
        }
    }

    public function getById(int $id): void {
        $data = $this->userModel->getById($id);
        if ($data != null) {
            $this->send(HTTPCodes::OK, $data, "OK");
        } else {
            $this->send(HTTPCodes::NO_CONTENT);
        }
    }

    public function getByIdFoyer(int $idFoyer): void {
        $data = $this->userModel->getByIdFoyer($idFoyer);
        if ($data != null) {
            $this->send(HTTPCodes::OK, $data, "OK");
        } else {
            $this->send(HTTPCodes::NO_CONTENT);
        }
    }

    public function getByIdRole(int $idRole): void {
        $data = $this->userModel->getByIdRole($idRole);
        if ($data != null) {
            $this->send(HTTPCodes::OK, $data, "OK");
        } else {
            $this->send(HTTPCodes::NO_CONTENT);
        }
    }

    public function getByIdRef(int $idRef): void {
        $data = $this->userModel->getByIdRef($idRef);
        if ($data != null) {
            $this->send(HTTPCodes::OK, $data, "OK");
        } else {
            $this->send(HTTPCodes::NO_CONTENT);
        }
    }

    public function add(): void {
        $validation =  \Config\Services::validation();

        $validation->setRules([
            "nom" => "required|max_length[30]",
            "prenom" => "required|max_length[30]",
            "password" => "required|regex_match[" . Regex::PASSWORD . "]",
            "idFoyer" => "required|integer",
            "idRole" => "required|integer"
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
        } else {
            $data = $this->request->getJSON(true);
            $data["password"] = $this->encodePassword($data["password"]);

            $data["login"] = $this->randomLogin($data["nom"], $data["prenom"]);
            
            $id = $this->userModel->add($data["nom"], $data["prenom"], $data["login"], $data["password"], $data["idRole"], $data["idFoyer"]);

            $data["id"] = $id;

            $this->send(HTTPCodes::OK, $data, "User added");
        }
    }

    public function updateLastLogin(): void {
        $validation =  \Config\Services::validation();

        $validation->setRules([
            "id" => "required|integer"
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
        } else {
            $data = $this->request->getJSON(true);

            $this->userModel->updateLastLogin($data["id"]);

            $this->send(HTTPCodes::OK, $data, "Last login updated");
        }
    }

    public function updateLastLogout(): void {
        $validation =  \Config\Services::validation();

        $validation->setRules([
            "id" => "required|integer"
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
        } else {
            $data = $this->request->getJSON(true);

            $this->userModel->updateLastLogout($data["id"]);

            $this->send(HTTPCodes::OK, $data, "Last logout updated");
        }
    }

    public function updateData(): void {
        $validation =  \Config\Services::validation();

        $validation->setRules([
            "id" => "required|integer",
            "nom" => "permit_empty|max_length[30]",
            "prenom" => "permit_empty|max_length[30]"
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
        } else {
            $data = $this->request->getJSON(true);

            if (isset($data["password"])) {
                unset($data["password"]); // We don't want to update the password with this method
            }

            if (isset($data["login"])) {
                unset($data["login"]); // We don't want to update the login
            }

            $this->userModel->updateData($data);

            $this->send(HTTPCodes::OK, $data, "User updated");
        }
    }

    /**
     * It updates the password of a user
     * 
     * Verfications:
     * - The old password is correct
     * - The new password is different from the old one
     * - The new password is valid
     */
    public function updatePassword(): void {
        $validation =  \Config\Services::validation();

        $validation->setRules([
            "id" => "required|integer",
            "oldPassword" => "required|regex_match[" . Regex::PASSWORD . "]",
            "newPassword" => "required|regex_match[" . Regex::PASSWORD . "]"
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
        } else {
            $data = $this->request->getJSON(true);

            // Check if the new password is different from the old one
            if ($data["oldPassword"] == $data["newPassword"]) {
                $this->send(HTTPCodes::BAD_REQUEST, null, "Error", "New password is the same as the old one");
                return;
            }

            $user = $this->userModel->getById($data["id"]);

            // Check if the old password is correct
            if (!password_verify($data["oldPassword"], $user["password"])) {
                $this->send(HTTPCodes::BAD_REQUEST, null, "Error", "Old password is incorrect");
            } else {
                $data["newPassword"] = $this->encodePassword($data["newPassword"]);

                $this->userModel->updatePassword($data["id"], $data["newPassword"]);

                $this->send(HTTPCodes::OK, null, "Password updated");
            }
        }
    }

    private function encodePassword(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    private function randomLogin(string $nom, string $prenom): string {
        $login = strtolower($prenom[0] . $nom);

        $i = 0;
        while ($this->userModel->getByLogin($login) != null) {
            $login = strtolower($prenom[0] . $nom . $i);
            $i++;
        }

        return $login;
    }
}
