<?php

namespace App\Controllers;

use App\Models\CodeModel;
use App\Models\RegistrationModel;
use App\Models\UserModel;
use App\Utils\HTTPCodes;
use App\Utils\UtilsCredentials;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class RegistrationController extends BaseController {

    private UserModel $userModel;
    private CodeModel $codeModel;
    private RegistrationModel $registrationModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        parent::initController($request, $response, $logger);

        $this->userModel         = new UserModel();
        $this->codeModel         = new CodeModel();
        $this->registrationModel = new RegistrationModel();
    }

    public function getAll() {
        if ($this->user->isDeveloper()) {
            $this->send(HTTPCodes::OK, $this->registrationModel->getAll(), "All registrations");
        } else {
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }

    public function register() {
        $validation = \Config\Services::validation();
        $validation->setRuleGroup("registration_add_validation");

        // Valider les données
        if (!$validation->withRequest($this->request)->run()) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
        }

        $data = $this->request->getJSON();

        // Vérifier si le code existe
        $code = $this->codeModel->getByCode($data->code);

        if ($code == null) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, "Code doesn't exist");
        }

        // Vérifier la validité du code
        if (!$this->codeModel->isValid($code->id)) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, "Invalid code");
        }

        // Ajouter nouvel utilisateur
        $login = UtilsCredentials::getValidRandomLogin($data->lastname, $data->firstname);
        $hashedPassword = password_hash($data->password, PASSWORD_DEFAULT);

        $idUser = $this->userModel->add($data->lastname, $data->firstname, $login, $hashedPassword, $code->idRole, $code->idFoyer);

        if ($idUser == -1) {
            return $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, null, "Error while adding user");
        }

        // Ajouter l'enregistrement
        $status = $this->registrationModel->add($code->id, $idUser);

        if ($status == -1) {
            return $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, null, "Error while adding registration");
        }

        // Passer le code à utilisé
        $this->codeModel->setUsed($code->id);

        // Renvoyer le statut de la requête
        $this->send(HTTPCodes::OK, null, "User added");
    }
}
