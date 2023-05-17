<?php

namespace App\Controllers;

use App\Utils\HTTPCodes;

use App\Models\FriendModel;
use App\Models\UserModel;
use App\Models\FriendRequestModel;
use App\Models\BlockedModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class FriendController extends BaseController {

    private const USER_NOT_FOUND                         = "Utilisateur introuvable";
    private const FRIENDS_OF_USER                        = "Amis de ";
    private const ACCESS_OTHER_RELATIONS                 = "Tentative d'accès à d'autres relations";
    private const CANNOT_ASK_SELF_FRIEND                 = "Impossible de demander si quelqu'un est ami avec lui-même";
    private const USERS_ALREADY_FRIENDS                  = "Les utilisateurs sont déjà amis";
    private const USERS_NOT_FRIENDS                      = "Les utilisateurs ne sont pas amis";
    private const CANNOT_BE_FRIEND_WITH_SELF             = "Impossible d'être ami avec soi-même";
    private const USERS_BLOCKED                          = "Utilisateurs bloqués";
    private const FRIEND_REQUEST_ALREADY_EXISTS          = "La demande d'ami existe déjà";
    private const RESOURCE_ADDED                         = "Ressource ajoutée";
    private const ERROR_ASKING_FRIEND                    = "Erreur lors de la demande d'ami";
    private const NO_REQUEST_SENT                        = "Aucune demande envoyée";
    private const RESOURCE_REMOVED                       = "Ressource supprimée";
    private const SUPPRESSION_NOT_ALLOWED                = "Suppression non autorisée";
    private const INSERTION_NOT_ALLOWED                  = "Insertion non autorisée";
    private const ERROR_REMOVING_FRIEND_REQUEST          = "Erreur lors de la suppression de la demande d'ami";
    private const ADD_RELATION_BETWEEN_USERS_NOT_ALLOWED = "Ajout de relation entre utilisateurs non autorisé";

    private FriendModel $friendModel;
    private UserModel $userModel;
    private FriendRequestModel $friendRequestModel;
    private BlockedModel $blockedModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        parent::initController($request, $response, $logger);

        $this->friendModel = new FriendModel();
        $this->friendRequestModel = new FriendRequestModel();
        $this->userModel = new UserModel();
        $this->blockedModel = new BlockedModel();
    }

    public function getAll($idUser) {
        if ($this->userModel->getById($idUser) == null) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, self::USER_NOT_FOUND);
        }

        if (!$this->user->isDeveloper()) {
            if ($this->user->getId() == $idUser) {
                $this->send(HTTPCodes::OK, $this->friendModel->getAll($idUser), self::FRIENDS_OF_USER . $idUser);
            } else {
                $this->send(HTTPCodes::BAD_REQUEST, null, self::ACCESS_OTHER_RELATIONS);
            }
        } else {
            $this->send(HTTPCodes::OK, $this->friendModel->getAll($idUser), self::FRIENDS_OF_USER . $idUser);
        }
    }

    public function isFriend(int $idUser, int $idFriend) {
        if ($idUser != $idFriend) {
            $this->send(HTTPCodes::OK, ["data" => $this->friendModel->isFriend($idUser, $idFriend)]);
        } else {
            $this->send(HTTPCodes::BAD_REQUEST, null, self::CANNOT_ASK_SELF_FRIEND);
        }
    }

    public function askFriend(int $idUser, $idFriend) {
        if ($this->userModel->getById($idUser) == null || $this->userModel->getById($idFriend) == null) {
            return $this->send(HTTPCodes::NOT_FOUND, null, self::USER_NOT_FOUND);
        }
        if ($this->friendModel->isFriend($idUser, $idFriend) != null) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, self::USERS_ALREADY_FRIENDS);
        }
        if ($idUser == $idFriend) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, self::CANNOT_BE_FRIEND_WITH_SELF);
        }

        if (!$this->blockedModel->isBlocked($idUser, $idFriend)) {
            return $this->send(HTTPCodes::NOT_ALLOWED, null, self::USERS_BLOCKED);
        }

        if (!$this->user->isDeveloper()) {
            if ($this->user->getId() == $idUser) {

                if ($this->friendRequestModel->isPending($idUser, $idFriend) == null) {
                    $result = $this->friendRequestModel->askFriend($idUser, $idFriend);
                    if ($result) {
                        $this->send(HTTPCodes::CREATED, null, self::RESOURCE_ADDED);
                    } else {
                        $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, null, self::ERROR_ASKING_FRIEND);
                    }
                } else {
                    $this->send(HTTPCodes::NOT_FOUND, null, self::FRIEND_REQUEST_ALREADY_EXISTS);
                }
            } else {
                $this->send(HTTPCodes::NOT_ALLOWED, null, self::ACCESS_OTHER_RELATIONS);
            }
        } else {
            if ($this->friendRequestModel->isPending($idUser, $idFriend) == null) {
                $result = $this->friendModel->add($idUser, $idFriend);
                if ($result) {
                    $this->send(HTTPCodes::CREATED, null, self::RESOURCE_ADDED);
                } else {
                    $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, null, self::ERROR_ASKING_FRIEND);
                }
            } else {
                $this->send(HTTPCodes::BAD_REQUEST);
            }
        }
    }

    public function add(int $idUser, $idFriend) {
        if ($this->userModel->getById($idUser) == null || $this->userModel->getById($idFriend) == null) {
            return $this->send(HTTPCodes::NOT_FOUND, null, self::USER_NOT_FOUND);
        }
        if ($this->friendModel->isFriend($idUser, $idFriend) != null) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, self::USERS_ALREADY_FRIENDS);
        }
        if ($idUser == $idFriend) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, self::CANNOT_BE_FRIEND_WITH_SELF);
        }

        if (!$this->blockedModel->isBlocked($idUser, $idFriend)) {
            return $this->send(HTTPCodes::NOT_ALLOWED, null, self::USERS_BLOCKED);
        }

        if (!$this->user->isDeveloper()) {

            if ($this->user->getId() == $idUser) {

                if ($this->friendModel->isPending($idUser, $idFriend) != null && $this->friendRequestModel->isNotRequester($idUser, $idFriend)) {
                    $result = $this->friendModel->add($idUser, $idFriend);
                    if ($result) {
                        $this->friendRequestModel->remove($idUser, $idFriend);
                        $this->send(HTTPCodes::CREATED, null, self::RESOURCE_ADDED);
                    } else {
                        $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, null, self::ERROR_ASKING_FRIEND);
                    }
                } else {
                    $this->send(HTTPCodes::BAD_REQUEST, null, self::NO_REQUEST_SENT);
                }
            } else {
                $this->send(HTTPCodes::NOT_ALLOWED, null, self::ADD_RELATION_BETWEEN_USERS_NOT_ALLOWED);
            }
        } else {
            $result = $this->friendModel->add($idUser, $idFriend);
            if ($result) {
                $this->send(HTTPCodes::CREATED, null, self::RESOURCE_ADDED);
            } else {
                $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, null, self::ERROR_ASKING_FRIEND);
            }
        }
    }

    public function rejectRequest($idUser, $idFriend) {
        if ($this->userModel->getById($idUser) == null || $this->userModel->getById($idFriend) == null) {
            return $this->send(HTTPCodes::NOT_FOUND, null, self::USER_NOT_FOUND);
        }

        if ($idUser == $idFriend) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, self::CANNOT_BE_FRIEND_WITH_SELF);
        }

        if (!$this->user->isDeveloper()) {
            if ($this->user->getId() == $idUser || $this->user->getId() == $idFriend) {

                if ($this->friendRequestModel->isPending($idUser, $idFriend) != null) {
                    $result = $this->friendRequestModel->remove($idUser, $idFriend);
                    if ($result) {
                        $this->send(HTTPCodes::CREATED, null, self::RESOURCE_REMOVED);
                    } else {
                        $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, null, self::ERROR_REMOVING_FRIEND_REQUEST);
                    }
                } else {
                    $this->send(HTTPCodes::BAD_REQUEST, null, self::NO_REQUEST_SENT);
                }
            } else {
                $this->send(HTTPCodes::NOT_ALLOWED, null, self::INSERTION_NOT_ALLOWED);
            }
        } else {
            if ($this->friendRequestModel->isPending($idUser, $idFriend) != null) {
                $result = $this->friendRequestModel->remove($idUser, $idFriend);
                if ($result) {
                    $this->send(HTTPCodes::CREATED, null, self::RESOURCE_ADDED);
                } else {
                    $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, null, self::ERROR_REMOVING_FRIEND_REQUEST);
                }
            } else {
                $this->send(HTTPCodes::BAD_REQUEST, null, self::NO_REQUEST_SENT);
            }
        }
    }

    public function remove(int $idUser, $idFriend) {
        if ($this->userModel->getById($idUser) == null || $this->userModel->getById($idFriend) == null) {
            return $this->send(HTTPCodes::NOT_FOUND, null, self::USER_NOT_FOUND);
        }
        if ($this->friendModel->isFriend($idUser, $idFriend) == null) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, self::USERS_NOT_FRIENDS);
        }

        if (!$this->user->isDeveloper()) {
            if (($this->user->getId() == $idUser || $this->user->getId() == $idFriend) && $this->friendModel->isFriend($idUser, $idFriend)) {
                $this->friendModel->remove($idUser, $idFriend);
                $this->send(HTTPCodes::CREATED, null, self::RESOURCE_REMOVED);
            } else {
                $this->send(HTTPCodes::NOT_ALLOWED, null, self::SUPPRESSION_NOT_ALLOWED);
            }
        } else {
            $this->friendRequestModel->remove($idUser, $idFriend);
            $this->send(HTTPCodes::CREATED, null, self::RESOURCE_REMOVED);
        }
    }
}
