<?php

namespace App\Controllers;

use App\Models\RoleModel;
use App\Utils\HTTPCodes;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class RoleController extends BaseController {

    private RoleModel $roleModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        parent::initController($request, $response, $logger);

        $this->roleModel = new RoleModel();
    }

    public function getAll() {
        if ($this->user->isDeveloper()) {
            $this->send(200, $this->roleModel->getAll(), "All roles");
        } else {
            $this->send(HTTPCodes::FORBIDDEN);
        }
    }
}
