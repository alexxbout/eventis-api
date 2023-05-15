<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Utils\HTTPCodes;
use Psr\Log\LoggerInterface;

class UserController extends BaseController {

    private $userModel;

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

        $validation->setRuleGroup("user_add_validation");

        if (!$validation->withRequest($this->request)->run()) {
            $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
            return;
        }
        $data = $this->request->getJSON(true);

        $id = self::addUser($this->userModel, $data["lastname"], $data["firstname"], $data["password"], $data["idFoyer"], $data["idRole"]);

        $data["id"] = $id;

        $this->send(HTTPCodes::OK, $data, "User added");
    }

    public function updateLastLogin(): void {
        $validation =  \Config\Services::validation();

        $validation->setRuleGroup("user_update_login_logout_validation");

        if (!$validation->withRequest($this->request)->run()) {
            $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
            return;
        }
        $data = $this->request->getJSON(true);

        $this->userModel->updateLastLogin($data["id"]);

        $this->send(HTTPCodes::OK, $data, "Last login updated");
    }

    public function updateLastLogout(): void {
        $validation =  \Config\Services::validation();

        $validation->setRuleGroup("user_update_login_logout_validation");

        if (!$validation->withRequest($this->request)->run()) {
            $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
            return;
        }
        $data = $this->request->getJSON(true);

        $this->userModel->updateLastLogout($data["id"]);

        $this->send(HTTPCodes::OK, $data, "Last logout updated");
    }

    public function updateData(): void {
        $validation =  \Config\Services::validation();

        $validation->setRuleGroup("user_update_data_validation");

        if (!$validation->withRequest($this->request)->run()) {
            $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
            return;
        }

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

        $validation->setRuleGroup("user_update_password_validation");

        if (!$validation->withRequest($this->request)->run()) {
            $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
            return;
        }

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
            return;
        }

        $data["newPassword"] = password_hash($data["newPassword"], PASSWORD_DEFAULT);

        $this->userModel->updatePassword($data["id"], $data["newPassword"]);

        $this->send(HTTPCodes::OK, null, "Password updated");
    }

    /**
     * It adds a user to the database with a unique login and a hashed password
     * 
     * @param UserModel userModel the user model
     * @param string lastname
     * @param string firstname
     * @param string rawPassword the password that the user will enter and that will be hashed
     * @param int idRole the id of the role
     * @param int idFoyer the id of the household the user belongs to
     * 
     * @return int The id of the user that has been added.
     */
    public static function addUser(UserModel $userModel, string $lastname, string $firstname, string $rawPassword, int $idRole, int $idFoyer): int {
        $login = self::getValidRandomLogin($userModel, $lastname, $firstname);
        $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);

        return $userModel->add($lastname, $firstname, $login, $hashedPassword, $idRole, $idFoyer);
    }

    /**
     * It generates a valid random login for a user
     * 
     * @param UserModel userModel the user model
     * @param string lastname
     * @param string firstname
     * 
     * @return string the login
     */
    public static function getValidRandomLogin(UserModel $userModel, string $lastname, string $firstname): string {
        $login = strtolower($firstname[0] . $lastname);

        $i = 0;
        while ($userModel->getByLogin($login) != null) {
            $login = strtolower($firstname[0] . $lastname . $i);
            $i++;
        }

        return $login;
    }
}
