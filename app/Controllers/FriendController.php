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

    private FriendModel $friendModel;
    private UserModel $userModel;
    private FriendRequestModel $friendRequestModel;
    private BlockedModel $blockedModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        parent::initController($request, $response, $logger);

        $this->friendModel = new FriendModel();
        $this->friendRequestModel = new FriendRequestModel();
        $this->userModel = new UserModel();
    }

    public function getAll($idUser) {
        if ($this->userModel->getById($idUser) == null) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, "User don't exist");
        }

        if (!$this->user->isDeveloper()) {
            if ($this->user->getId() == $idUser) {
                $this->send(HTTPCodes::OK, $this->friendModel->getAll($idUser), "Friends of " . $idUser);
            } else {
                $this->send(HTTPCodes::BAD_REQUEST, null, "Attempting to access to others relations");
            }
        } else {
            $this->send(HTTPCodes::OK, $this->friendModel->getAll($idUser), "Friends of " . $idUser);
        }
    }

    public function isFriend(int $idUser, int $idFriend) {
        if ($idUser != $idFriend) {
            $this->send(HTTPCodes::OK, ["data" => $this->friendModel->isFriend($idUser, $idFriend)]);
        } else {
            $this->send(HTTPCodes::BAD_REQUEST, null, "Cannot ask if someone is friend with himself");
        }
    }

    public function askFriend(int $idUser, $idFriend) {
        if ($this->userModel->getById($idUser) == null || $this->userModel->getById($idFriend) == null) {
            return $this->send(HTTPCodes::NOT_FOUND, null, "One of the users don't exist");
        }
        if ($this->friendModel->isFriend($idUser, $idFriend) != null) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, "Users are already friends");
        }
        if ($idUser == $idFriend) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, "People cannot be friend with themself");
        }

        if (!$this->blockedModel->isBlocked($idUser, $idFriend)) {
            return $this->send(HTTPCodes::NOT_ALLOWED, null, "Users are blocked");
        }


        if (!$this->user->isDeveloper()) {
            if ($this->user->getId() == $idUser) {

                if ($this->friendRequestModel->isPending($idUser, $idFriend) == null) {
                    $result = $this->friendRequestModel->askFriend($idUser, $idFriend);
                    if ($result) {
                        $this->send(HTTPCodes::CREATED, null, "Ressource added");
                    } else {
                        $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, null, "Error while asking friend");
                    }
                } else {
                    $this->send(HTTPCodes::NOT_FOUND, null, "Friend request already exists");
                }
            } else {
                $this->send(HTTPCodes::NOT_ALLOWED, ["Attempting to add realtion beetween two users"], "Insertion not allowed");
            }
        } else {
            if ($this->friendRequestModel->isPending($idUser, $idFriend) == null) {
                $result = $this->friendModel->add($idUser, $idFriend);
                if ($result) {
                    $this->send(HTTPCodes::CREATED, null, "Ressource added");
                } else {
                    $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, null, "Error while asking friend");
                }
            } else {
                $this->send(HTTPCodes::BAD_REQUEST, null, "");
            }
        }
    }

    public function add(int $idUser, $idFriend) {
        if ($this->userModel->getById($idUser) == null || $this->userModel->getById($idFriend) == null) {
            return $this->send(HTTPCodes::NOT_FOUND, null, "One of the users don't exist");
        }
        if ($this->friendModel->isFriend($idUser, $idFriend) != null) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, "Users are already friends");
        }
        if ($idUser == $idFriend) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, "People cannot be friend with themself");
        }

        if (!$this->blockedModel->isBlocked($idUser, $idFriend)) {
            return $this->send(HTTPCodes::NOT_ALLOWED, null, "Users are blocked");
        }

        if (!$this->user->isDeveloper()) {

            if ($this->user->getId() == $idUser) {

                if ($this->friendModel->isPending($idUser, $idFriend) != null && $this->friendRequestModel->isNotRequester($idUser, $idFriend)) {
                    $result = $this->friendModel->add($idUser, $idFriend);
                    if ($result) {
                        $this->friendRequestModel->remove($idUser, $idFriend);
                        $this->send(HTTPCodes::CREATED, null, "Ressource added");
                    } else {
                        $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, null, "Error while adding friend");
                    }
                } else {
                    $this->send(HTTPCodes::BAD_REQUEST, null, "No request sent");
                }
            } else {
                $this->send(HTTPCodes::NOT_ALLOWED, null, "Attempting to add realtion beetween two users");
            }
        } else {
            $result = $this->friendModel->add($idUser, $idFriend);
            if ($result) {
                $this->send(HTTPCodes::CREATED, null, "Ressource added");
            } else {
                $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, null, "Error while adding friend");
            }
        }
    }

    public function rejectRequest($idUser, $idFriend) {
        if ($this->userModel->getById($idUser) == null || $this->userModel->getById($idFriend) == null) {
            return $this->send(HTTPCodes::NOT_FOUND, null, "One of the users don't exist");
        }

        if ($idUser == $idFriend) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, "People cannot be friend with themself");
        }

        if (!$this->user->isDeveloper()) {
            if ($this->user->getId() == $idUser || $this->user->getId() == $idFriend) {

                if ($this->friendRequestModel->isPending($idUser, $idFriend) != null) {
                    $result = $this->friendRequestModel->remove($idUser, $idFriend);
                    if ($result) {
                        $this->send(HTTPCodes::CREATED, null, "Ressource removed");
                    } else {
                        $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, null, "Error while removing friend request");
                    }
                } else {
                    $this->send(HTTPCodes::BAD_REQUEST, null, "No request sent");
                }
            } else {
                $this->send(HTTPCodes::NOT_ALLOWED, null, "Insertion not allowed");
            }
        } else {
            if ($this->friendRequestModel->isPending($idUser, $idFriend) != null) {
                $result = $this->friendRequestModel->remove($idUser, $idFriend);
                if ($result) {
                    $this->send(HTTPCodes::CREATED, null, "Ressource added");
                } else {
                    $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, null, "Error while removing friend request");
                }
            } else {
                $this->send(HTTPCodes::BAD_REQUEST, null, "No request sent");
            }
        }
    }

    public function remove(int $idUser, $idFriend) {
        if ($this->userModel->getById($idUser) == null || $this->userModel->getById($idFriend) == null) {
            return $this->send(HTTPCodes::NOT_FOUND, null, "One of the users don't exist");
        }
        if ($this->friendModel->isFriend($idUser, $idFriend) == null) {
            return $this->send(HTTPCodes::BAD_REQUEST, null, "Users are not friends");
        }

        if (!$this->user->isDeveloper()) {
            if (($this->user->getId() == $idUser || $this->user->getId() == $idFriend) && $this->friendModel->isFriend($idUser, $idFriend)) {
                $this->friendModel->remove($idUser, $idFriend);
                $this->send(HTTPCodes::CREATED, null, "Ressource removed");
            } else {
                $this->send(HTTPCodes::NOT_ALLOWED, null, "Suppression not allowed");
            }
        } else {
            $this->friendRequestModel->remove($idUser, $idFriend);
            $this->send(HTTPCodes::CREATED, null, "Ressource removed");
        }
    }
}
