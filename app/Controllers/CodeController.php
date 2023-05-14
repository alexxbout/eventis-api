<?php

namespace App\Controllers;

use App\Models\CodeModel;
use App\Models\FoyerModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Utils\HTTPCodes;
use App\Utils\UtilsRoles;
use Psr\Log\LoggerInterface;
use DateTime;

class CodeController extends BaseController {

    private const CODE_LENGTH = 6;
    private const MAX_CODE_VALIDITY = 7; // En jours

    private CodeModel $codeModel;
    private FoyerModel $foyerModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        parent::initController($request, $response, $logger);
        $this->codeModel = new \App\Models\CodeModel();
        $this->foyerModel = new \App\Models\FoyerModel();
    }

    public function getAll() {
        if (!$this->user->isDeveloper()) {
            return $this->send(HTTPCodes::FORBIDDEN);
        } else {
            $data = $this->codeModel->getAll();
            if ($data != null) {
                // Ajouter l'information de validité pour chaque code
                foreach ($data as &$code) {
                    $code->valid = $this->codeModel->isValid($code->id);
                }
                return $this->send(HTTPCodes::OK, $data, "");
            }
        }
    }

    public function getAllByFoyer(int $idFoyer) {
        if ($this->user->isUser()) {
            return $this->send(HTTPCodes::FORBIDDEN);
        } else {
            $data = $this->codeModel->getAllByFoyer($idFoyer);
            if ($data != null) {
                // Ajouter l'information de validité pour chaque code
                foreach ($data as &$code) {
                    $code->valid = $this->codeModel->isValid($code->id);
                }
                return $this->send(HTTPCodes::OK, $data, "");
            }
        }
    }

    public function getByCode(string $code) {
        $data = $this->codeModel->getByCode($code);
        if ($data != null) {
            $data->valid = $this->codeModel->isValid($data->id);
            return $this->send(HTTPCodes::OK, $data, "");
        } else {
            return $this->send(HTTPCodes::NO_CONTENT);
        }
    }

    public function add() {
        if ($this->user->isUser()) {
            return $this->send(HTTPCodes::FORBIDDEN);
        }

        $validation =  \Config\Services::validation();
        $validation->setRuleGroup("code_add_validation");

        if (!$validation->withRequest($this->request)->run()) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
        }

        $data = $this->request->getJSON();

        // Vérifier que le role existe
        if (!UtilsRoles::isValidRole($data->idRole)) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, "Invalid role");
        }

        // Vérifier que le code n'est pas valide plus de MAX_CODE_VALIDITY jours
        $validDate = new DateTime();
        $validDate->modify("+" . self::MAX_CODE_VALIDITY . " days");

        if (new DateTime($data->expire) > $validDate) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, "Expire date is too far in the future");
        }

        // Vérifier que le foyer existe
        if ($this->foyerModel->getById($data->idFoyer) == null) {
            return $this->send(HTTPCodes::NOT_FOUND, null, "Foyer not found");
        }

        // Vérifier que l'utilisateur a le droit de générer un code pour un rôle
        // Les administrateurs ne peuvent pas générer de code pour un autre administrateur ou développeur
        // Les éducateurs peuvent générer de code seulement pour des utilisateurs 
        if ($this->user->isAdmin()) {
            if ($data->idRole == UtilsRoles::ADMIN || $data->idRole == UtilsRoles::DEVELOPER) {
                return $this->send(HTTPCodes::FORBIDDEN);
            }
        } else if ($this->user->isEducator()) {
            if ($data->idRole != UtilsRoles::USER) {
                return $this->send(HTTPCodes::FORBIDDEN);
            }
        }

        $code = "";

        // Générer un code unique
        do {
            $code = $this->getRandomCode();
        } while ($this->codeModel->getByCode($code) != null);

        $this->codeModel->add($code, $data->idFoyer, $this->user->getId(), $data->idRole, $data->expire);

        $this->send(HTTPCodes::OK, ["code" => $code], "Code generated");
    }

    /**
     * It generates a random string of CODE_LENGTH characters
     * 
     * @return string A string of CODE_LENGTH random characters.
     */
    private function getRandomCode(): string {
        $code = "";
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        for ($i = 0; $i < CodeController::CODE_LENGTH; $i++) {
            $code .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $code;
    }
}
