<?php

namespace App\Controllers;

use App\Utils\HTTPCodes;

use App\Models\InteretModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class InterestController extends BaseController {

    private const SUCCESS                 = "Centres d'interets trouvés";
    private const NO_CONTENT              = "Rien n'a été trouvé";
    private const USER_NOT_FOUND          = "Utilisateur introuvable";
    private const ACCESS_OTHER_INTEREST   = "Tentative d'accès aux centres d'interets des autres";
    private const USER_ALREADY_INTERESTED = "L'utilisateur possède déjà ce centre d'intérêt";
    private const USER_NOT_INTERESTED     = "L'utilisateur ne possède pas ce centre d'intérêt";
    private const REQUEST_SENT            = "Envoyée";
    private const NO_REQUEST_SENT         = "Aucune demande envoyée";
    private const RESOURCE_REMOVED        = "Ressource supprimée";
    private const SUPPRESSION_NOT_ALLOWED = "Suppression non autorisée";
    private const INSERTION_NOT_ALLOWED   = "Insertion non autorisée";
    private const INVALID_ROLE            = "Rôle invalide";
    private const REQUEST_FAILED          = "La requète a échouée";
    private const USER_NOT_USER           = "Vous ne pouvez pas ajouter de centre d'intérêts à un autre utilisateur";

    private InteretModel $interetModel;
    private UserModel $userModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        parent::initController($request, $response, $logger);

        $this->interetModel = new InteretModel();
        $this->userModel = new UserModel();
    }

    public function getAll() {
        $data = $this->interetModel->getAll();
        if (empty($data)) {
            $this->send(HTTPCodes::NO_CONTENT, $data, self::NO_CONTENT);
        } else {
            $this->send(HTTPCodes::OK, $data, self::SUCCESS);
        }
    }

    public function getInterestsByUser($idUser) {
        if ($this->userModel->getById($idUser) == null) {
            return $this->send(HTTPCodes::NOT_FOUND, null, self::USER_NOT_FOUND);
        } else {
            $data = $this->interetModel->getInterestByUser($idUser);
            if (empty($data)) {
                $this->send(HTTPCodes::NO_CONTENT, $data, self::NO_CONTENT);
            } else {
                $this->send(HTTPCodes::OK, $data, self::SUCCESS);
            }
        }
    }

    public function add(int $idUser, int $idInteret) {
        if ($this->userModel->getById($idUser) == null) {
            return $this->send(HTTPCodes::NOT_FOUND, null, self::USER_NOT_FOUND);
        }

        if (!$this->user->isDeveloper()) {
            if ($this->user->getId() == $idUser) {
                if ($this->interetModel->hasInterest($idUser, $idInteret)) {
                    return $this->send(HTTPCodes::BAD_REQUEST, null, self::USER_ALREADY_INTERESTED);
                }
                $result = $this->interetModel->add($idUser, $idInteret);
                if ($result) {
                    $this->send(HTTPCodes::CREATED, null, self::REQUEST_SENT);
                } else {
                    $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, null, self::REQUEST_FAILED);
                }
            } else {
                $this->send(HTTPCodes::FORBIDDEN, null, self::USER_NOT_USER);
            }
        } else {
            if ($this->interetModel->hasInterest($idUser, $idInteret)) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, self::USER_ALREADY_INTERESTED);
            }
            $result = $this->interetModel->add($idUser, $idInteret);
            if ($result) {
                $this->send(HTTPCodes::CREATED, null, self::REQUEST_SENT);
            } else {
                $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, null, self::REQUEST_FAILED);
            }
        }
    }

    public function remove(int $idUser, int $idInteret) {
        if ($this->userModel->getById($idUser) == null) {
            return $this->send(HTTPCodes::NOT_FOUND, null, self::USER_NOT_FOUND);
        }
        if (!$this->interetModel->hasInterest($idUser, $idInteret)) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, self::USER_NOT_INTERESTED);
        }

        if (!$this->user->isDeveloper()) {
            if (($this->user->getId() == $idUser) && $this->interetModel->hasInterest($idUser, $idInteret)) {
                $result = $this->interetModel->remove($idUser, $idInteret);
                if ($result) {
                    $this->send(HTTPCodes::OK, null, self::RESOURCE_REMOVED);
                } else {
                    $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, null, self::REQUEST_FAILED);
                }
            } else {
                $this->send(HTTPCodes::FORBIDDEN, null, self::SUPPRESSION_NOT_ALLOWED);
            }
        } else {
            $result = $this->interetModel->remove($idUser, $idInteret);
            if ($result) {
                $this->send(HTTPCodes::OK, null, self::RESOURCE_REMOVED);
            } else {
                $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, null, self::REQUEST_FAILED);
            }
        }
    }
}
