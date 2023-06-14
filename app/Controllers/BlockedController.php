<?php

namespace App\Controllers;

use App\Models\BlockedModel;
use App\Utils\HTTPCodes;
use App\Models\FriendModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class BlockedController extends BaseController {

    private const ALL                     = "Tous les utilisateurs bloqués";
    private const NO_CONTENT              = "Rien n'a été trouvé";
    private const BLOCKED_USER            = "Utilisateur bloqué";
    private const USER_ALREADY_BLOCKED    = "Utilisateur déjà bloqué";
    private const CANNOT_BLOCK_YOURSELF   = "Vous ne pouvez pas vous bloquer vous-même";
    private const CANNOT_UNBLOCK_YOURSELF = "Vous ne pouvez pas vous débloquer vous-même";
    private const USER_UNBLOCKED          = "Utilisateur débloqué";
    private const USER_NOT_BLOCKED        = "Utilisateur n'était pas bloqué";
    private const NOT_YOUR_ACC            = "Vous ne pouvez pas gérer le compte de quelqu'un d'autre";
    private const USER_DOESNT_EXIST       = "Utilisateur n'existe pas";
    private const USER_SAME_ID            = "Les deux utilisateurs fournis sont les mêmes ";
    private const INVALID_ROLE            = "Rôle invalide";

    private BlockedModel $blockedModel;
    private FriendModel $friendModel;
    private UserModel $userModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        parent::initController($request, $response, $logger);

        $this->blockedModel = new BlockedModel();
        $this->friendModel = new FriendModel();
        $this->userModel = new UserModel();
    }

    public function getAll(int $idUser) {
        //dev peut tout voir, autres utilisateurs ne voient que leurs propres listes bloquees
        if ($this->user->isDeveloper() || $this->user->getId() == $idUser) {
            $exists = $this->blockedModel->getAll($idUser);
            if (empty($exists)) {
                $this->send(HTTPCodes::NO_CONTENT, $exists, self::NO_CONTENT);
            } else {
                $this->send(HTTPCodes::OK, $exists, self::ALL);
            }
        } else {
            $this->send(HTTPCodes::FORBIDDEN, null, self::NOT_YOUR_ACC);
        }
    }

    public function add(int $idUser, int $idBlocked) {
        if ($this->user->isDeveloper() || $this->user->getId() == $idUser) {
            if ($this->userModel->getById($idBlocked) == null) {
                $this->send(HTTPCodes::NOT_FOUND, null, self::USER_DOESNT_EXIST);
            } else if ($idUser == $idBlocked) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, self::CANNOT_BLOCK_YOURSELF);
            }

            $isInTable =  $this->blockedModel->isBlocked($idUser, $idBlocked);

            if (!$isInTable) {

                if ($this->friendModel->isFriend($idUser, $idBlocked)) {
                    $this->friendModel->remove($idUser, $idBlocked);
                }

                $this->blockedModel->add($idUser, $idBlocked);
                $this->send(HTTPCodes::CREATED, null, self::BLOCKED_USER);
            } else {
                $this->send(HTTPCodes::BAD_REQUEST, null, self::USER_ALREADY_BLOCKED);
            }
        } else {
            $this->send(HTTPCodes::FORBIDDEN, null, self::NOT_YOUR_ACC);
        }
    }

    public function remove(int $idUser, int $idBlocked) {
        if ($this->user->isDeveloper() || $this->user->getId() == $idUser) {
            if ($this->userModel->getById($idBlocked) == NULL) {
                $this->send(HTTPCodes::NOT_FOUND, null, self::USER_DOESNT_EXIST);
            } else if ($idUser == $idBlocked) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, self::CANNOT_UNBLOCK_YOURSELF);
            }

            $isInTable =  $this->blockedModel->isBlocked($idUser, $idBlocked);

            if ($isInTable) {
                $this->blockedModel->remove($idUser, $idBlocked);
                $this->send(HTTPCodes::OK, null, self::USER_UNBLOCKED);
            } else {
                $this->send(HTTPCodes::NOT_FOUND, null, self::USER_NOT_BLOCKED);
            }
        } else {
            $this->send(HTTPCodes::FORBIDDEN, null, self::NOT_YOUR_ACC);
        }
    }

    public function isBlocked(int $idUser, int $idUser2) {
        if ($this->userModel->getById($idUser) == null || $this->userModel->getById($idUser2) == null) {
            return $this->send(HTTPCodes::NOT_FOUND, null, self::USER_DOESNT_EXIST);
        } else if ($idUser == $idUser2) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, self::USER_SAME_ID);
        }

        if ($this->user->isDeveloper() || $this->user->getId() == $idUser) {
            return $this->send(HTTPCodes::OK, ["blocked" => $this->blockedModel->isBlocked($idUser, $idUser2)]);
        } else {
            $this->send(HTTPCodes::FORBIDDEN, null, self::INVALID_ROLE);
        }
    }
}
