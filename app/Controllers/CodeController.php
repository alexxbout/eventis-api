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
    private const EXPIRE_DATE_TOO_FAR        = "La date d'expiration est trop éloignée dans le futur";
    private const EXPIRE_DATE_ALREADY_PASSED = "La date d'expiration est déjà passée";
    private const FOYER_NOT_FOUND            = "Foyer introuvable";
    private const FORBIDDEN_ADMIN_DEVELOPER  = "Les administrateurs ne peuvent pas générer de code pour un autre administrateur ou développeur";
    private const FORBIDDEN_EDUCATOR         = "Les éducateurs peuvent générer de code seulement pour des utilisateurs";
    private const CODE_GENERATED             = "Code généré";
    private const VALIDATION_ERROR           = "Erreur de validation";
    private const ONE_CODE                   = "Code valide";

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

            //NO_CONTENT s'il y en a pas
            if(empty($data)) return $this->send(HTTPCodes::NO_CONTENT, $data, self::NO_CONTENT);

            // Ajouter l'information de validité pour chaque code
            foreach ($data as &$code) {
                $code->valid = $this->codeModel->isValid($code->id);
            }

            return $this->send(HTTPCodes::OK, $data, self::ALL_CODES);
        }
    }

    public function getAllByFoyer(int $idFoyer) {
        //interdit pour les utilisateur et les educateur qui cherchent en dehors de leurs foyers
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

        $validation =  \Config\Services::validation();
        $validation->setRuleGroup("code_add_validation");

        if (!$validation->withRequest($this->request)->run()) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, self::VALIDATION_ERROR, $validation->getErrors());
        }

        $data = $this->request->getJSON();

        // Vérifier que le role existe
        if (!UtilsRoles::isValidRole($data->idRole)) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, self::INVALID_ROLE);
        }

        // Vérifier que le code n'est pas valide plus de MAX_CODE_VALIDITY jours
        $validDate = new DateTime();
        $validDate->modify("+" . self::MAX_CODE_VALIDITY . " days");

        if (new DateTime($data->expire) > $validDate) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, self::EXPIRE_DATE_TOO_FAR);
        }

        // Vérifier que le code n'est pas déjà expiré
        if (new DateTime($data->expire) < new DateTime()) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, self::EXPIRE_DATE_ALREADY_PASSED);
        }

        // Vérifier que le foyer existe
        if ($this->foyerModel->getById($data->idFoyer) == null) {
            return $this->send(HTTPCodes::NOT_FOUND, null, self::FOYER_NOT_FOUND);
        }

        // Vérifier que l'utilisateur a le droit de générer un code pour un rôle
        // Les administrateurs ne peuvent pas générer de code pour un autre administrateur ou développeur
        // Les éducateurs peuvent générer de code seulement pour des utilisateurs 
        if ($this->user->isAdmin()) {
            if ($data->idRole == UtilsRoles::ADMIN || $data->idRole == UtilsRoles::DEVELOPER) {
                return $this->send(HTTPCodes::FORBIDDEN, null, self::FORBIDDEN_ADMIN_DEVELOPER);
            }
        } else if ($this->user->isEducator()) {
            if ($data->idRole != UtilsRoles::USER) {
                return $this->send(HTTPCodes::FORBIDDEN, null, self::FORBIDDEN_EDUCATOR);
            }
        }

        $code = "";

        // Générer un code unique
        do {
            $code = UtilsRegistrationCode::getRandom();
        } while ($this->codeModel->getByCode($code) != null);

        $this->codeModel->add($code, $data->idFoyer, $this->user->getId(), $data->idRole, $data->expire);

        $this->send(HTTPCodes::CREATED, ["code" => $code], self::CODE_GENERATED);
    }
}
