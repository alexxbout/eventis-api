<?php

namespace App\Controllers;

use App\Models\BlockedModel;
use App\Utils\HTTPCodes;
use App\Models\FriendModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class BlockedController extends BaseController {

    private BlockedModel $blockedModel;
    private FriendModel $friendModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        parent::initController($request, $response, $logger);

        $this->blockedModel = new BlockedModel();
    }

    public function getAll(int $idUser) {
        $this->send(HTTPCodes::OK, $this->blockedModel->getAll($idUser), "Blocked users");
    }

    public function add(int $idUser, int $idBlocked) {
        if ($idUser == $idBlocked) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, "Cannot block yourself");
        }

        $isInTable =  $this->blockedModel->isBlocked($idUser, $idBlocked);

        if (!$isInTable) {

            if ($this->friendModel->isFriend($idUser, $idBlocked)) {
                $this->friendModel->remove($idUser, $idBlocked);
            }

            $this->blockedModel->add($idUser, $idBlocked);
            $this->send(HTTPCodes::OK, null, "User blocked");
        } else {
            $this->send(HTTPCodes::BAD_REQUEST, null, "User already blocked");
        }
    }

    public function remove(int $idUser, int $idBlocked) {
        if ($idUser == $idBlocked) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, "Cannot unblock yourself");
        }

        $isInTable =  $this->blockedModel->isBlocked($idUser, $idBlocked);

        if ($isInTable && $idUser == $this->user->getId()) {
            $this->blockedModel->remove($idUser, $idBlocked);
            $this->send(HTTPCodes::OK, null, "User unblocked");
        } else {
            $this->send(HTTPCodes::NOT_FOUND, null, "User was not blocked");
        }
    }
}
