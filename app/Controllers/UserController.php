<?php

namespace App\Controllers;

use App\Models\FoyerModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Utils\HTTPCodes;
use App\Utils\UtilsCredentials;
use Psr\Log\LoggerInterface;

class UserController extends BaseController {

    private UserModel $userModel;
    private FoyerModel $foyerModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        parent::initController($request, $response, $logger);

        $this->userModel = new \App\Models\UserModel();
        $this->foyerModel = new \App\Models\FoyerModel();
    }

    public function getAll() {
        if ($this->user->isDeveloper()) {
            $this->send(HTTPCodes::OK, $this->userModel->getAll(), "All users");
        } else {
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }

    public function getById(int $id) {
        if ($this->user->isDeveloper() || $this->user->isAdmin()) {
            $this->send(HTTPCodes::OK, $this->userModel->getById($id), "User with id " . $id);
        } else {
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }

    public function getByIdFoyer(int $idFoyer) {
        if ($this->user->isDeveloper() || $this->user->isAdmin() || $this->user->isEducator()) {
            $this->send(HTTPCodes::OK, $this->userModel->getByIdFoyer($idFoyer), "All users of foyer with id " . $idFoyer);
        } else {
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }

    public function add() {
        if ($this->user->isDeveloper()) {
            $validation =  \Config\Services::validation();
            $validation->setRuleGroup("user_add_validation");

            if (!$validation->withRequest($this->request)->run()) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
            }
            $data = $this->request->getJSON();

            // Check if idFoyer exists
            if ($this->foyerModel->getById($data->idFoyer) === null) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, "idFoyer doesn't exists");
            }

            $login = UtilsCredentials::getValidRandomLogin($data->lastname, $data->firstname);
            $hashedPassword = password_hash($data->password, PASSWORD_DEFAULT);

            // Update password field to new hashed password
            $data->password = $hashedPassword;

            $id = $this->userModel->add($data->lastname, $data->firstname, $login, $hashedPassword, $data->idRole, $data->idFoyer);

            if ($id != -1) {
                $data->id = $id;

                $this->send(HTTPCodes::OK, $data, "User added");
            } else {
                $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, null, "Error while adding user");
            }
        } else {
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }

    public function update(int $idUser) {
        // Check idUser and if acc ID corresponds with id of user
        if ($this->user->isDeveloper() || $this->user->isAdmin() || $idUser == $this->user->getId()) {
            $validation =  \Config\Services::validation();
            $validation->setRuleGroup("user_update_data_validation");

            if (!$validation->withRequest($this->request)->run()) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
            }

            $data = $this->request->getJSON();

            // Remove password/id from body

            if (isset($data->password)) {
                unset($data->password); // We don't want to update the password with this method
            }

            if (isset($data->id)) {
                unset($data->id); // We don't want to update the password with this method
            }

            // Check if lastname and firstname are not empty
            // TODO

            $this->userModel->updateData($idUser, $data);

            $this->send(HTTPCodes::OK, $data, "User updated");
        }
        // User tries to modify an account other than their own OR is not an admin/dev
        else {
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }

    /**
     * Deactivates account: changes boolean value in USER table from 1 to 0 (active to inactive)
     * 
     * Verifications:
     * - either the person is an dev/admin 
     * - or the person is deactivating their own account
     */
    public function deactivateAccount(int $idUser) {
        // Check idUser and if acc ID corresponds with id of user
        if ($this->user->isDeveloper() || $this->user->isAdmin() || $idUser == $this->user->getId()) {
            $this->userModel->setActive($idUser, 0);

            $this->send(HTTPCodes::OK, null, "User deactivated");
        } else {
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }

    /**
     * Deactivates account: changes boolean value in USER table from 1 to 0 (active to inactive)
     * 
     * Verifications:
     * - either the person is an dev/admin 
     * - or the person is deactivating their own account
     */
    public function reactivateAccount(int $idUser) {
        // Either account is dev/admin check idUser and if acc ID corresponds with id of user
        if ($this->user->isDeveloper() || $this->user->isAdmin() || $idUser == $this->user->getId()) {
            $this->userModel->setActive($idUser, 1);

            $this->send(HTTPCodes::OK, null, "User activated");
        } else {
            $this->send(HTTPCodes::UNAUTHORIZED);
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
    public function updatePassword(int $idUser) {
        if ($this->user->isDeveloper() || $idUser == $this->user->getId()) {
            $validation =  \Config\Services::validation();
            $validation->setRuleGroup("user_update_password_validation");

            if (!$validation->withRequest($this->request)->run()) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
            }

            $data = $this->request->getJSON();

            // Check if the new password is different from the old one
            if ($data->oldPassword == $data->newPassword) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, "Error", "New password is the same as the old one");
            }

            $user = $this->userModel->getById($data->id);

            // Check if the old password is correct
            if (!password_verify($data->oldPassword, $user->password)) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, "Error", "Old password is incorrect");
            }

            $data->newPassword = password_hash($data->newPassword, PASSWORD_DEFAULT);

            $this->userModel->updatePassword($data->id, $data->newPassword);

            $this->send(HTTPCodes::OK, null, "Password updated");
        } else {
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }
}
