<?php

namespace App\Controllers;

use App\Models\FoyerModel;
use App\Utils\HTTPCodes;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class FoyerController extends BaseController {

    private const NO_CONTENT                = "Rien n'a été trouvé";
    private const ALL_FOYERS                = "Tous les foyers";
    private const ALL_FOYERS_BY_ZIP         = "Tous les foyers du code postal ";
    private const FOYER_ADDED               = "Le foyer a été ajouté";
    private const INVALID_ROLE               = "Rôle invalide";
    private const VALIDATION_ERROR          = "Erreur de validation";

    private FoyerModel $foyerModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        parent::initController($request, $response, $logger);

        $this->foyerModel = new FoyerModel();
    }

    public function getAll() {
        $data = $this->foyerModel->getAll();
        if(empty($data)){
            $this->send(HTTPCodes::NO_CONTENT, $data, self::NO_CONTENT);
        } else {
            $this->send(HTTPCodes::OK, $data, self::ALL_FOYERS);
        }
    }

    public function getAllByZip(int $zip) {
        $data = $this->foyerModel->getByZip($zip);
        if(empty($data)){
            $this->send(HTTPCodes::NO_CONTENT, $data, self::NO_CONTENT);
        } else {
            $this->send(HTTPCodes::OK, $data, self::ALL_FOYERS_BY_ZIP . $zip);
        }
        
    }

    public function add() {
        if ($this->user->isAdmin() || $this->user->isDeveloper()) {
            $validation =  \Config\Services::validation();
            $validation->setRuleGroup("foyer_add_validation");

            if (!$validation->withRequest($this->request)->run()) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, self::VALIDATION_ERROR, $validation->getErrors());
            }

            $data = $this->request->getJSON();

            $this->foyerModel->add($data);

            $this->send(HTTPCodes::CREATED, $data, self::FOYER_ADDED);
        } else {
            $this->send(HTTPCodes::FORBIDDEN, null, self::INVALID_ROLE);
        }
    }
}
