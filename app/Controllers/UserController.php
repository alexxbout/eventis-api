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
    private const NO_CONTENT               = "Rien n'a été trouvé";
    private const ID_USER_DOESNT_EXIST     = "Utilisateur inconnu";
    private const ALL_USERS_OF_FOYER       = "Tous les utilisateurs du foyer ";
    private const VALIDATION_ERROR         = "Erreur de validation";
    private const ID_FOYER_DOESNT_EXIST    = "L'identifiant de foyer n'existe pas";
    private const USER_ADDED               = "Utilisateur ajouté";
    private const ERROR_ADDING_USER        = "Erreur lors de l'ajout de l'utilisateur";
    private const USER_UPDATED             = "Utilisateur mis à jour";
    private const FORBIDDEN                = "Non autorisé";
    private const USER_DEACTIVATED         = "Utilisateur désactivé";
    private const USER_ACTIVATED           = "Utilisateur activé";
    private const PASSWORD_UPDATED         = "Mot de passe mis à jour";
    private const OLD_PASSWORD_INCORRECT   = "Ancien mot de passe incorrect";
    private const NEW_PASSWORD_SAME_AS_OLD = "Le nouveau mot de passe est identique à l'ancien";
    private const FILE_ALREADY_MOVED       = "Le fichier a déjà été déplacé";
    private const IMAGE_UPLOADED           = "Image téléchargée";

    private const PROFIL_PICTURE_PATH      = WRITEPATH . "uploads/images/users/";

    private UserModel $userModel;
    private FoyerModel $foyerModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        parent::initController($request, $response, $logger);

        $this->userModel = new \App\Models\UserModel();
        $this->foyerModel = new \App\Models\FoyerModel();
    }

    public function getAll() {
        if ($this->user->isDeveloper()) {
            $data = $this->userModel->getAll();
            if(empty($data)){
                $this->send(HTTPCodes::NO_CONTENT, null, self::NO_CONTENT);
            } else {
                $this->send(HTTPCodes::OK, $data, self::ALL_USERS);
            }
            
        } else {
            $this->send(HTTPCodes::FORBIDDEN, null, self::FORBIDDEN);
        }
    }

    public function getById(int $id) {
        if ($this->user->isDeveloper() || $this->user->isAdmin()) {
            $data = $this->userModel->getById($id);
            if($data == null){
                $this->send(HTTPCodes::NOT_FOUND, null, self::ID_USER_DOESNT_EXIST);
            } else {
                $this->send(HTTPCodes::OK, $data, self::USER_WITH_ID . $id);
            }
            
        } else {
            $this->send(HTTPCodes::FORBIDDEN, null, self::FORBIDDEN);
        }
    }

    public function getByIdFoyer(int $idFoyer) {
        if ($this->user->isDeveloper() || $this->user->isAdmin() || $this->user->isEducator()) {
            if($this->foyerModel->getById($idFoyer) == null){
                $this->send(HTTPCodes::NOT_FOUND, null, self::ID_FOYER_DOESNT_EXIST);
            }
            $data = $this->userModel->getByIdFoyer($idFoyer);
            if(empty($data)){
                $this->send(HTTPCodes::NO_CONTENT, $data, self::NO_CONTENT);
            } else {
                $this->send(HTTPCodes::OK, $data, self::ALL_USERS_OF_FOYER . $idFoyer);
            }
            
        } else {
            $this->send(HTTPCodes::FORBIDDEN, null, self::FORBIDDEN);
        }
    }

    public function add() {
        if ($this->user->isDeveloper()) {
            $validation =  \Config\Services::validation();
            $validation->setRuleGroup("user_add_validation");

            if (!$validation->withRequest($this->request)->run()) {
                $this->send(HTTPCodes::BAD_REQUEST, null, self::VALIDATION_ERROR, $validation->getErrors());
                return;
            }
            $data = $this->request->getJSON();

            // Vérifier si l'idFoyer existe
            if ($this->foyerModel->getById($data->idFoyer) === null) {
                $this->send(HTTPCodes::NOT_FOUND, null, self::ID_FOYER_DOESNT_EXIST);
                return;
            }

            $login = UtilsCredentials::getValidRandomLogin($data->lastname, $data->firstname);
            $hashedPassword = password_hash($data->password, PASSWORD_DEFAULT);

            // Mettre à jour le champ du mot de passe avec le nouveau mot de passe haché
            $data->password = $hashedPassword;
            $id = $this->userModel->add($data->lastname, $data->firstname, $login, $hashedPassword, $data->idRole, $data->idFoyer);

            if ($id != -1) {
                $data->id = $id;
                $this->send(HTTPCodes::CREATED, $data, self::USER_ADDED);
            } else {
                $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, null, self::ERROR_ADDING_USER);
            }
        } else {
            $this->send(HTTPCodes::FORBIDDEN, null, self::FORBIDDEN);
        }
    }

    public function update(int $idUser) {
        // Vérifier si l'idUser correspond à l'ID de l'utilisateur ou si l'utilisateur est un développeur ou un administrateur
        if ($this->user->isDeveloper() || $this->user->isAdmin() || $idUser == $this->user->getId()) {
            if($this->userModel->getById($idUser == null)){
                $this->send(HTTPCodes::NOT_FOUND, null, self::ID_USER_DOESNT_EXIST);
            }
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

            if (isset($data->idFoyer)) {
                unset($data->idFoyer); // Nous ne voulons pas mettre à jour l'ID avec cette méthode
            }

            // Vérifier si le nom de famille et le prénom ne sont pas vides
            // TODO

            $this->userModel->updateData($idUser, $data);

            $this->send(HTTPCodes::OK, $data, self::USER_UPDATED);
        }
        // L'utilisateur essaie de modifier un compte autre que le sien OU n'est pas un administrateur / développeur
        else {
            $this->send(HTTPCodes::FORBIDDEN, null, self::FORBIDDEN);
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
            if($this->userModel->getById($idUser == null)){
                $this->send(HTTPCodes::NOT_FOUND, null, self::ID_USER_DOESNT_EXIST);
            }
            $this->userModel->setActive($idUser, 0);

            $this->send(HTTPCodes::OK, null, self::USER_DEACTIVATED);
        } else {
            $this->send(HTTPCodes::FORBIDDEN, null, self::FORBIDDEN);
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
            if($this->userModel->getById($idUser == null)){
                $this->send(HTTPCodes::NOT_FOUND, null, self::ID_USER_DOESNT_EXIST);
            }
            $this->userModel->setActive($idUser, 1);

            $this->send(HTTPCodes::OK, null, self::USER_ACTIVATED);
        } else {
            $this->send(HTTPCodes::FORBIDDEN, null, self::FORBIDDEN);
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
            if($this->userModel->getById($idUser) == null){
                $this->send(HTTPCodes::NOT_FOUND, null, self::ID_USER_DOESNT_EXIST);
            }
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
            $this->send(HTTPCodes::FORBIDDEN, null, self::FORBIDDEN);
        }
    }

    public function addProfilPicture(int $idUser) {
        if ($this->user->isDeveloper() || $idUser == $this->user->getId()) {
            if($this->userModel->getById($idUser) == null){
                $this->send(HTTPCodes::NOT_FOUND, null, self::ID_USER_DOESNT_EXIST);
            }
            $validation =  \Config\Services::validation();
            $validation->setRuleGroup("addImage_validation");

            if (!$validation->withRequest($this->request)->run()) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, self::VALIDATION_ERROR, $validation->getErrors());
            }

            $imageName = $this->userModel->getProfilPicture($idUser);
            if ($imageName != NULL) {
                // Check if image has already been uploaded with the same name
                if (file_exists(self::PROFIL_PICTURE_PATH . $imageName)) {
                    unlink(self::PROFIL_PICTURE_PATH . $imageName);
                }
            }

            $file = $this->request->getFile("image");

            if ($file->hasMoved()) {
                $this->send(HTTPCodes::BAD_REQUEST, null, self::FILE_ALREADY_MOVED);
            } else {
                // Generate random name
                $newName = $file->getRandomName();
                $file->move(self::PROFIL_PICTURE_PATH, $newName);

                // Optimize image
                \Config\Services::image()
                    ->withFile(self::PROFIL_PICTURE_PATH . $newName)
                    ->save(self::PROFIL_PICTURE_PATH . $newName, 30);

                // Save image name in database
                $this->userModel->setProfilPicture($idUser, $newName);

                $this->send(HTTPCodes::OK, ["file" => $newName], self::IMAGE_UPLOADED);
            }
        } else {
            $this->send(HTTPCodes::FORBIDDEN, null, self::FORBIDDEN);
        }
    }
}
