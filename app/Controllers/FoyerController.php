<?php

namespace App\Controllers;

use App\Models\FoyerModel;
use App\Utils\HTTPCodes;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class FoyerController extends BaseController {

    private const ALL_FOYERS                = "Tous les foyers";
    private const ALL_FOYERS_BY_ZIP         = "Tous les foyers du code postal ";
    private const FOYER_ADDED               = "Le foyer a été ajouté";

    private const VALIDATION_ERROR          = "Erreur de validation";

    private FoyerModel $foyerModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        parent::initController($request, $response, $logger);

        $this->foyerModel = new FoyerModel();
    }

    public function getAll() {
        $this->send(HTTPCodes::OK, $this->foyerModel->getAll(), self::ALL_FOYERS);
    }

    public function getAllByZip(int $zip) {
        $this->send(HTTPCodes::OK, $this->foyerModel->getByZip($zip), self::ALL_FOYERS_BY_ZIP . $zip);
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

            $this->send(HTTPCodes::OK, $data, self::FOYER_ADDED);
        } else {
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }
}
