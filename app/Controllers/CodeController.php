<?php

namespace App\Controllers;

use App\Models\CodeModel;
use App\Models\FoyerModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Utils\HTTPCodes;
use App\Utils\UtilsRegistrationCode;
use App\Utils\UtilsRoles;
use Psr\Log\LoggerInterface;
use DateTime;

class CodeController extends BaseController {

    private const MAX_CODE_VALIDITY = 7; // En jours

    private const NO_CONTENT                 = "Rien n'a été trouvé";
    private const ALL_CODES                  = "Tous les codes";
    private const ALL_CODES_FOR_FOYER        = "Tous les codes pour le foyer";
    private const CODE_NOT_FOUND             = "Code introuvable";
    private const CODE_USED                  = "Code déjà utilisé ou expiré";
    private const INVALID_ROLE               = "Rôle invalide";
    private const CODE_GENERATED             = "Code généré";
    private const VALIDATION_ERROR           = "Erreur de validation";
    private const ONE_CODE                   = "Code valide";
    private const EXPIRE_DATE_INVALID        = "Date d'expiration invalide";
    private const FOYER_NOT_FOUND            = "Foyer introuvable";
    private const INVALID_ROLE_OR_FOYER      = "Vous devez spécifier le rôle et l'id du foyer.";
    private const INVALID_USER_RIGHTS        = "Vous n'avez pas les droits pour effectuer cette action";

    private CodeModel $codeModel;
    private FoyerModel $foyerModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        parent::initController($request, $response, $logger);
        $this->codeModel = new CodeModel();
        $this->foyerModel = new FoyerModel();
    }

    public function getAll() {
        if (!$this->user->isDeveloper()) {
            return $this->send(HTTPCodes::FORBIDDEN, null, self::INVALID_ROLE);
        } else {
            $data = $this->codeModel->getAll();

            // NO_CONTENT s'il y en a pas
            if(empty($data)) return $this->send(HTTPCodes::NO_CONTENT, $data, self::NO_CONTENT);

            // Ajouter l'information de validité pour chaque code
            foreach ($data as &$code) {
                $code->valid = $this->codeModel->isValid($code->id);
            }

            return $this->send(HTTPCodes::OK, $data, self::ALL_CODES);
        }
    }

    public function getAllByFoyer(int $idFoyer) {
        // Interdit pour les utilisateur et les educateur qui cherchent en dehors de leurs foyers
        if ($this->user->isUser() || ($this->user->isEducator() && $this->user->getIdFoyer() != $idFoyer)) {
            return $this->send(HTTPCodes::FORBIDDEN, null, self::INVALID_ROLE);
        } else {
            $data = $this->codeModel->getAllByFoyer($idFoyer);

            //NO_CONTENT s'il y en a pas
            if(empty($data)) return $this->send(HTTPCodes::NO_CONTENT, $data, self::NO_CONTENT);

            // Ajouter l'information de validité pour chaque code
            foreach ($data as &$code) {
                $code->valid = $this->codeModel->isValid($code->id);
            }
            return $this->send(HTTPCodes::OK, $data, self::ALL_CODES_FOR_FOYER . ' ' . $idFoyer);
        }
    }

    public function getByCode(string $code) { 
        $data = $this->codeModel->getByCode($code);
        if($data == null){
            return $this->send(HTTPCodes::NOT_FOUND, null, self::CODE_NOT_FOUND);
        }
        $valid = $this->codeModel->isValid($data->id);
        if(!$valid) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, self::CODE_USED);
        } else {
            return $this->send(HTTPCodes::OK, null, self::ONE_CODE);
        }
    }

    public function add() {
        if ($this->user->isUser()) {
            return $this->send(HTTPCodes::FORBIDDEN, null, self::INVALID_ROLE);
        }
    
        $validation = \Config\Services::validation();
        $validation->setRuleGroup("code_add_validation");
    
        if (!$validation->withRequest($this->request)->run()) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, self::VALIDATION_ERROR, $validation->getErrors());
        }
    
        $data = $this->request->getJSON();
    
        if (!$this->validateCodeExpiration($data->expire)) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, self::EXPIRE_DATE_INVALID);
        }
    
        $userRightsValidation = $this->validateUserRights($data);
        if ($userRightsValidation !== true) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, $userRightsValidation);
        }
    
        $roleAndFoyerValidation = $this->validateRoleAndFoyer($data->idRole, $data->idFoyer);
        if ($roleAndFoyerValidation !== true) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, $roleAndFoyerValidation);
        }
    
        $code = $this->generateUniqueCode();
        $this->codeModel->add($code, $data->idFoyer, $this->user->getId(), $data->idRole, $data->expire);
    
        $this->send(HTTPCodes::CREATED, ["code" => $code], self::CODE_GENERATED);
    }
    
    private function validateUserRights($data): string | bool {
        if ($this->user->isEducator()) {
            $data->idRole = UtilsRoles::USER;
            $data->idFoyer = $this->user->getIdFoyer();
        } else if ($this->user->isAdmin() || $this->user->isDeveloper()) {
            if (!isset($data->idRole) || !isset($data->idFoyer)) {
                if ($this->user->isDeveloper()) {
                    return self::INVALID_ROLE_OR_FOYER;
                } else if ($this->user->isAdmin()) {
                    return self::INVALID_ROLE_OR_FOYER;
                }
            }
    
            if ($this->user->isAdmin() && ($data->idRole == UtilsRoles::ADMIN || $data->idRole == UtilsRoles::DEVELOPER)) {
                if ($this->user->isDeveloper()) {
                    return self::INVALID_USER_RIGHTS;
                } else if ($this->user->isAdmin()) {
                    return self::INVALID_USER_RIGHTS;
                }
            }
        } else {
            return self::INVALID_ROLE;
        }
    
        return true;
    }
    
    private function validateRoleAndFoyer($idRole, $idFoyer): string | bool {
        if (!UtilsRoles::isValidRole($idRole)) {
            return self::INVALID_ROLE;
        }
    
        if ($this->foyerModel->getById($idFoyer) == null) {
            return self::FOYER_NOT_FOUND;
        }
    
        return true;
    }

    private function validateCodeExpiration($expire) {
        $validDate = new DateTime();
        $validDate->modify("+" . self::MAX_CODE_VALIDITY . " days");
    
        $expireDate = new DateTime($expire);
    
        return $expireDate <= $validDate && $expireDate > new DateTime();
    }
    
    
    private function generateUniqueCode(): string {
        do {
            $code = UtilsRegistrationCode::getRandom();
        } while ($this->codeModel->getByCode($code) != null);
    
        return $code;
    }   
}