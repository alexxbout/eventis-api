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

    private const ALL_USERS                = "Tous les utilisateurs";
    private const USER_WITH_ID             = "Utilisateur ";
    private const ALL_USERS_OF_FOYER       = "Tous les utilisateurs du foyer ";
    private const VALIDATION_ERROR         = "Erreur de validation";
    private const ID_FOYER_DOESNT_EXIST    = "L'identifiant de foyer n'existe pas";
    private const USER_ADDED               = "Utilisateur ajouté";
    private const ERROR_ADDING_USER        = "Erreur lors de l'ajout de l'utilisateur";
    private const USER_UPDATED             = "Utilisateur mis à jour";
    private const UNAUTHORIZED             = "Non autorisé";
    private const USER_DEACTIVATED         = "Utilisateur désactivé";
    private const USER_ACTIVATED           = "Utilisateur activé";
    private const PASSWORD_UPDATED         = "Mot de passe mis à jour";
    private const OLD_PASSWORD_INCORRECT   = "Ancien mot de passe incorrect";
    private const NEW_PASSWORD_SAME_AS_OLD = "Le nouveau mot de passe est identique à l'ancien";

    private UserModel $userModel;
    private FoyerModel $foyerModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        parent::initController($request, $response, $logger);

        $this->userModel = new \App\Models\UserModel();
        $this->foyerModel = new \App\Models\FoyerModel();
    }

    public function getAll() {
        if ($this->user->isDeveloper()) {
            $this->send(HTTPCodes::OK, $this->userModel->getAll(), self::ALL_USERS);
        } else {
            $this->send(HTTPCodes::UNAUTHORIZED, null, self::UNAUTHORIZED);
        }
    }

    public function getById(int $id) {
        if ($this->user->isDeveloper() || $this->user->isAdmin()) {
            $this->send(HTTPCodes::OK, $this->userModel->getById($id), self::USER_WITH_ID . $id);
        } else {
            $this->send(HTTPCodes::UNAUTHORIZED, null, self::UNAUTHORIZED);
        }
    }

    public function getByIdFoyer(int $idFoyer) {
        if ($this->user->isDeveloper() || $this->user->isAdmin() || $this->user->isEducator()) {
            $this->send(HTTPCodes::OK, $this->userModel->getByIdFoyer($idFoyer), self::ALL_USERS_OF_FOYER . $idFoyer);
        } else {
            $this->send(HTTPCodes::UNAUTHORIZED, null, self::UNAUTHORIZED);
        }
    }

    public function add() {
        if ($this->user->isDeveloper()) {
            $validation =  \Config\Services::validation();
            $validation->setRuleGroup("user_add_validation");

            if (!$validation->withRequest($this->request)->run()) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, self::VALIDATION_ERROR, $validation->getErrors());
            }
            $data = $this->request->getJSON();

            // Vérifier si l'idFoyer existe
            if ($this->foyerModel->getById($data->idFoyer) === null) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, self::ID_FOYER_DOESNT_EXIST);
            }

            $login = UtilsCredentials::getValidRandomLogin($data->lastname, $data->firstname);
            $hashedPassword = password_hash($data->password, PASSWORD_DEFAULT);

            // Mettre à jour le champ du mot de passe avec le nouveau mot de passe haché
            $data->password = $hashedPassword;

            $id = $this->userModel->add($data->lastname, $data->firstname, $login, $hashedPassword, $data->idRole, $data->idFoyer);

            if ($id != -1) {
                $data->id = $id;

                $this->send(HTTPCodes::OK, $data, self::USER_ADDED);
            } else {
                $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, null, self::ERROR_ADDING_USER);
            }
        } else {
            $this->send(HTTPCodes::UNAUTHORIZED, null, self::UNAUTHORIZED);
        }
    }

    public function update(int $idUser) {
        // Vérifier si l'idUser correspond à l'ID de l'utilisateur ou si l'utilisateur est un développeur ou un administrateur
        if ($this->user->isDeveloper() || $this->user->isAdmin() || $idUser == $this->user->getId()) {
            $validation =  \Config\Services::validation();
            $validation->setRuleGroup("user_update_data_validation");

            if (!$validation->withRequest($this->request)->run()) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, self::VALIDATION_ERROR, $validation->getErrors());
            }

            $data = $this->request->getJSON();

            // Supprimer le mot de passe et l'ID du corps de la requête
            if (isset($data->password)) {
                unset($data->password); // Nous ne voulons pas mettre à jour le mot de passe avec cette méthode
            }

            if (isset($data->id)) {
                unset($data->id); // Nous ne voulons pas mettre à jour l'ID avec cette méthode
            }

            // Vérifier si le nom de famille et le prénom ne sont pas vides
            // TODO

            $this->userModel->updateData($idUser, $data);

            $this->send(HTTPCodes::OK, $data, self::USER_UPDATED);
        }
        // L'utilisateur essaie de modifier un compte autre que le sien OU n'est pas un administrateur / développeur
        else {
            $this->send(HTTPCodes::UNAUTHORIZED, null, self::UNAUTHORIZED);
        }
    }

    /**
     * Désactive le compte : change la valeur booléenne dans la table USER de 1 à 0 (actif à inactif)
     * 
     * Vérifications :
     * - soit la personne est un développeur / administrateur
     * - soit la personne désactive son propre compte
     */
    public function deactivateAccount(int $idUser) {
        // Vérifier si l'idUser correspond à l'ID de l'utilisateur ou si l'utilisateur est un développeur ou un administrateur
        if ($this->user->isDeveloper() || $this->user->isAdmin() || $idUser == $this->user->getId()) {
            $this->userModel->setActive($idUser, 0);

            $this->send(HTTPCodes::OK, null, self::USER_DEACTIVATED);
        } else {
            $this->send(HTTPCodes::UNAUTHORIZED, null, self::UNAUTHORIZED);
        }
    }

    /**
     * Réactive le compte : change la valeur booléenne dans la table USER de 1 à 0 (inactif à actif)
     * 
     * Vérifications :
     * - soit la personne est un développeur / administrateur
     * - soit la personne réactive son propre compte
     */
    public function reactivateAccount(int $idUser) {
        // Soit le compte est dev/admin vérifier si l'idUser correspond à l'ID de l'utilisateur ou si l'utilisateur est un développeur ou un administrateur
        if ($this->user->isDeveloper() || $this->user->isAdmin() || $idUser == $this->user->getId()) {
            $this->userModel->setActive($idUser, 1);

            $this->send(HTTPCodes::OK, null, self::USER_ACTIVATED);
        } else {
            $this->send(HTTPCodes::UNAUTHORIZED, null, self::UNAUTHORIZED);
        }
    }

    /**
     * Met à jour le mot de passe d'un utilisateur
     * 
     * Vérifications :
     * - L'ancien mot de passe est correct
     * - Le nouveau mot de passe est différent de l'ancien
     * - Le nouveau mot de passe est valide
     */
    public function updatePassword(int $idUser) {
        if ($this->user->isDeveloper() || $idUser == $this->user->getId()) {
            $validation =  \Config\Services::validation();
            $validation->setRuleGroup("user_update_password_validation");

            if (!$validation->withRequest($this->request)->run()) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, self::VALIDATION_ERROR, $validation->getErrors());
            }

            $data = $this->request->getJSON();

            // Vérifier si le nouveau mot de passe est différent de l'ancien
            if ($data->oldPassword == $data->newPassword) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, self::NEW_PASSWORD_SAME_AS_OLD);
            }

            $user = $this->userModel->getById($data->id);

            // Vérifier si l'ancien mot de passe est correct
            if (!password_verify($data->oldPassword, $user->password)) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, self::OLD_PASSWORD_INCORRECT);
            }

            $data->newPassword = password_hash($data->newPassword, PASSWORD_DEFAULT);

            $this->userModel->updatePassword($data->id, $data->newPassword);

            $this->send(HTTPCodes::OK, null, self::PASSWORD_UPDATED);
        } else {
            $this->send(HTTPCodes::UNAUTHORIZED, null, self::UNAUTHORIZED);
        }
    }
}
