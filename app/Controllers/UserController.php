<?php

namespace App\Controllers;

use App\Models\FoyerModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Utils\HTTPCodes;
use App\Utils\UtilsCredentials;
use Psr\Log\LoggerInterface;

class UserController extends BaseController
{

    private UserModel $userModel;
    private FoyerModel $foyerModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->userModel = new \App\Models\UserModel();

        $this->foyerModel = new \App\Models\FoyerModel();
    }

    public function getAll(): void
    {
        //reserver aux devs
        if ($this->user->isDeveloper()) {
            $data = $this->userModel->getAll();
            if ($data != null) {
                $this->send(HTTPCodes::OK, $data, "OK");
            } else {
                $this->send(HTTPCodes::NO_CONTENT);
            }
        } else {
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }

    public function getById(int $id): void
    {
        if ($this->user->isDeveloper() || $this->user->isAdmin()) {
            $data = $this->userModel->getById($id);
            if ($data != null) {
                $this->send(HTTPCodes::OK, $data, "OK");
            } else {
                $this->send(HTTPCodes::NO_CONTENT);
            }
        } else {
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }

    public function getByIdFoyer(int $idFoyer): void
    {
        if ($this->user->isDeveloper() || $this->user->isAdmin() || $this->user->isEducator()) {
            $data = $this->userModel->getByIdFoyer($idFoyer);
            if ($data != null) {
                $this->send(HTTPCodes::OK, $data, "OK");
            } else {
                $this->send(HTTPCodes::NO_CONTENT);
            }
        } else {
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }

    public function add(): void
    {
        if ($this->user->isDeveloper()) {
            $validation =  \Config\Services::validation();

            $validation->setRuleGroup("user_add_validation");

            if (!$validation->withRequest($this->request)->run()) {
                $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
                return;
            }
            $data = $this->request->getJSON();

            // Check if idFoyer exists
            if ($this->foyerModel->getById($data->idFoyer) === null) {
                $this->send(HTTPCodes::BAD_REQUEST, null, "idFoyer doesn't exists");
                return;
            }

            $login = UtilsCredentials::getValidRandomLogin($data->lastname, $data->firstname);
            $hashedPassword = password_hash($data->password, PASSWORD_DEFAULT);

            // Update password field to new hashed password
            $data->password = $hashedPassword;

            $id = $this->userModel->add($data->lastname, $data->firstname, $login, $hashedPassword, $data->idRole, $data->idFoyer);

            if (isset($id)) {
                $data->id = $id;

                $this->send(HTTPCodes::OK, $data, "User added");
            } else {
                $this->send(HTTPCodes::NO_CONTENT, null, "Unable to find retreive user id");
            }

        } else {
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }

    public function update(int $idUser): void
    {
        //check idUser and if acc ID corresponds with id of user
        if ($this->user->isDeveloper() || $this->user->isAdmin() || $idUser == $this->user->getId()) {
            $validation =  \Config\Services::validation();

            $validation->setRuleGroup("user_update_data_validation");

            if (!$validation->withRequest($this->request)->run()) {
                $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
                return;
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
        //user tries to modify an account other than their own OR is not an admin/dev
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
    public function deactivateAccount(int $idUser): void
    {
        //check idUser and if acc ID corresponds with id of user
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
    public function reactivateAccount(int $idUser): void
    {
        //either account is dev/admin check idUser and if acc ID corresponds with id of user
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
    public function updatePassword(int $idUser): void
    {
        if ($this->user->isDeveloper() || $idUser == $this->user->getId()) {
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
        } else {
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }
}
