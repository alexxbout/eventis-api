<?php

namespace App\Controllers;

use App\Models\FoyerModel;
use App\Utils\HTTPCodes;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class FoyerController extends BaseController
{

    private FoyerModel $foyerModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->foyerModel = new FoyerModel();
    }

    public function getAll()
    {
        $this->send(HTTPCodes::OK, $this->foyerModel->getAll(), "All foyers");
    }

    public function getAllByZip(int $zip)
    {
        $this->send(HTTPCodes::OK, $this->foyerModel->getByZip($zip), "All foyers of zip " . $zip);
    }

    public function add()
    {
        if ($this->user->isAdmin() || $this->user->isDeveloper()) {
            $validation =  \Config\Services::validation();
            $validation->setRuleGroup("foyer_add_validation");

            if (!$validation->withRequest($this->request)->run()) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
            }

            $data = $this->request->getJSON(true);

            $this->foyerModel->add($data);

            $this->send(HTTPCodes::OK, $data, "Foyer added");
        } else {
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }
}
