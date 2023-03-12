<?php

namespace App\Controllers;

use App\Models\CodeModel;
use App\Models\RegistrationModel;
use App\Models\UserModel;
use App\Utils\HTTPCodes;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class RegistrationController extends BaseController {

    private UserModel $userModel;
    private CodeModel $codeModel;
    private RegistrationModel $registrationModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        parent::initController($request, $response, $logger);

        $this->userModel         = new \App\Models\UserModel();
        $this->codeModel         = new \App\Models\CodeModel();
        $this->registrationModel = new \App\Models\RegistrationModel();
    }

    public function register() {
        // Validation des données code, nom, prenom, password
        $validation = \Config\Services::validation();

        $validation->setRuleGroup("registration_add_validation");

        // Valider les données
        if (!$validation->withRequest($this->request)->run()) {
            $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
            return;
        }
        $data = $this->request->getJSON(true);

        // Vérifier si le code existe
        $code = $this->codeModel->getByCode($data["code"]);
        if ($code == null) {
            $this->send(HTTPCodes::BAD_REQUEST, null, "Code doesn't exist");
            return;
        }

        // Vérifier la validité du code
        if (!CodeController::isValid($this->codeModel, $code["id"])) {
            $this->send(HTTPCodes::BAD_REQUEST, null, "Invalid code");
            return;
        }

        // Ajout du nouvel utilisateur
        // Extraire les données id du code et id du foyer
        $idCode = $code["id"];
        $idFoyer = $code["idFoyer"];

        $idRole = 2; // A modifier plus tard

        // Ajouter l'utilisateur
        $idUser = UserController::addUser($this->userModel, $data["nom"], $data["prenom"], $data["password"], $idRole, $idFoyer);

        // Ajouter l'enregistrement
        $this->registrationModel->add($idCode, $idUser);

        // Passer le code à utilisé
        $this->codeModel->setUsed($idCode);

        // Renvoyer le statut de la requête
        $this->send(HTTPCodes::OK, null, "User added");
    }
}
