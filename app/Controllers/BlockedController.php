<?php

namespace App\Controllers;

use App\Models\BlockedModel;
use App\Utils\HTTPCodes;
use App\Models\FriendModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class BlockedController extends BaseController {

    private const ALL                     = "Tous les utilisateurs bloqués";
    private const BLOCKED_USER            = "Utilisateur bloqué";
    private const USER_ALREADY_BLOCKED    = "Utilisateur déjà bloqué";
    private const CANNOT_BLOCK_YOURSELF   = "Vous ne pouvez pas vous bloquer vous-même";
    private const CANNOT_UNBLOCK_YOURSELF = "Vous ne pouvez pas vous débloquer vous-même";
    private const USER_UNBLOCKED          = "Utilisateur débloqué";
    private const USER_NOT_BLOCKED        = "Utilisateur n'était pas bloqué";

    private BlockedModel $blockedModel;
    private FriendModel $friendModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        parent::initController($request, $response, $logger);

        $this->blockedModel = new BlockedModel();
        $this->friendModel = new FriendModel();
    }

    public function getAll(int $idUser) {
        $this->send(HTTPCodes::OK, $this->blockedModel->getAll($idUser), self::ALL);
    }

    public function add(int $idUser, int $idBlocked) {
        if ($idUser == $idBlocked) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, self::CANNOT_BLOCK_YOURSELF);
        }

        $isInTable =  $this->blockedModel->isBlocked($idUser, $idBlocked);

        if (!$isInTable) {

            if ($this->friendModel->isFriend($idUser, $idBlocked)) {
                $this->friendModel->remove($idUser, $idBlocked);
            }

            $this->blockedModel->add($idUser, $idBlocked);
            $this->send(HTTPCodes::OK, null, self::BLOCKED_USER);
        } else {
            $this->send(HTTPCodes::BAD_REQUEST, null, self::USER_ALREADY_BLOCKED);
        }
    }

    public function remove(int $idUser, int $idBlocked) {
        if ($idUser == $idBlocked) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, self::CANNOT_UNBLOCK_YOURSELF);
        }

        $isInTable =  $this->blockedModel->isBlocked($idUser, $idBlocked);

        if ($isInTable && $idUser == $this->user->getId()) {
            $this->blockedModel->remove($idUser, $idBlocked);
            $this->send(HTTPCodes::OK, null, self::USER_UNBLOCKED);
        } else {
            $this->send(HTTPCodes::NOT_FOUND, null, self::USER_NOT_BLOCKED);
        }
    }
}
