<?php

namespace App\Controllers;

use App\Utils\HTTPCodes;

use App\Models\FriendModel;
use App\Models\UserModel;
use App\Models\FriendRequestModel;

class FriendController extends BaseController
{
    private FriendModel $friendModel;
    private UserModel $userModel;
    private FriendRequestModel $friendRequestModel;

    public function __construct()
    {
        $this->friendModel = new \App\Models\FriendModel();
        $this->friendRequestModel = new \App\Models\FriendRequestModel();
        $this->userModel = new \App\Models\UserModel();
    }

    public function getAll($idUser): void
    {

        if ($this->userModel->getById($idUser) == null) {
            $this->send(HTTPCodes::NOT_FOUND, ["User don't exist"], "Ressource not found");
            return;
        }

        if (!$this->user->isDeveloper()) {
            if ($this->user->getId() == $idUser) {
                $data = $this->friendModel->getAll($idUser);
                if (empty($data) || $data == null) {
                    $this->send(HTTPCodes::NO_CONTENT, ["You have no friends"]);
                } else {
                    $this->send(HTTPCodes::OK, $data);
                }
            } else {
                $this->send(HTTPCodes::BAD_REQUEST, ["Attempting to access to others relations"]);
            }
            //$idUser = $this->request->{'data'}->id;

        } else {
            $data = $this->friendModel->getAll($idUser);
            if (empty($data) || $data == null) {
                $this->send(HTTPCodes::NO_CONTENT, `{$idUser} has no friend`);
            } else {
                $this->send(HTTPCodes::OK, $data);
            }
        }
    }

    public function isFriend(int $idUser, int $idFriend): void
    {
        if ($idUser != $idFriend) {
            $data = $this->friendModel->isFriend($idUser, $idFriend);

            $this->send(HTTPCodes::OK, ["data" => $data != null]);
        } else {
            $err = ["message" => "Same arguments", "data" => "Cannot ask if someone is friend with himself"];
            $this->send(HTTPCodes::BAD_REQUEST, $err);
        }
    }



    public function askFriend(int $idUser, $idFriend)
    {
        if ($this->userModel->getById($idUser) == null || $this->userModel->getById($idFriend) == null) {
            $this->send(HTTPCodes::NOT_FOUND, ["One of the users don't exist"], "Ressource not found");
            return;
        }
        if ($this->friendModel->isFriend($idUser, $idFriend) != null) {
            $this->send(HTTPCodes::BAD_REQUEST, ["Users are already friends"], "Relation exist");
            return;
        }
        if ($idUser == $idFriend) {
            $this->send(HTTPCodes::BAD_REQUEST, ["People cannot be friend with themself "], "Same arguments");
            return;
        }

        //add is blocked 
        ############################
        ############################
        ############################
        ############################
        ############################


        if (!$this->user->isDeveloper()) {
            if ($this->user->getId() == $idUser) {

                if ($this->friendRequestModel->isPending($idUser, $idFriend) == null) {
                    $result = $this->friendRequestModel->askFriend($idUser, $idFriend);
                    if ($result) {
                        $this->send(HTTPCodes::CREATED, null, "Ressource added");
                    } else {
                        $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, ["Server failed to execute request"], "Request failed");
                    }
                } else {
                    $this->send(HTTPCodes::NO_CONTENT, null, null);
                }
            } else {
                $this->send(HTTPCodes::NOT_ALLOWED, ["Attempting to add realtion beetween two users"], "Insertion not allowed");
            }
        } else {
            if ($this->friendRequestModel->isPending($idUser, $idFriend) == null) {
                $result = $this->friendRequestModel->askFriend($idUser, $idFriend);
                if ($result) {
                    $this->send(HTTPCodes::CREATED, null, "Ressource added");
                } else {
                    $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, ["Server failed to execute request"], "Request failed");
                }
            } else {
                $this->send(HTTPCodes::NO_CONTENT, null, null);
            }
        }
    }



    public function add(int $idUser, $idFriend)
    {
        if ($this->userModel->getById($idUser) == null || $this->userModel->getById($idFriend) == null) {
            $this->send(HTTPCodes::NOT_FOUND, ["One of the users don't exist"], "Ressource not found");
            return;
        }
        if ($this->friendModel->isFriend($idUser, $idFriend) != null) {
            $this->send(HTTPCodes::BAD_REQUEST, ["Users are already friends"], "Relation exist");
            return;
        }
        if ($idUser == $idFriend) {
            $this->send(HTTPCodes::BAD_REQUEST, ["People cannot be friend with themself "], "Same arguments");
            return;
        }

        //add is blocked 
        ############################
        ############################
        ############################
        ############################
        ############################


        if (!$this->user->isDeveloper()) {
            if ($this->user->getId() == $idUser) {

                if ($this->friendModel->isPending($idUser, $idFriend) != null) {
                    $result = $this->friendModel->add($idUser, $idFriend);
                    if ($result) {
                        $this->send(HTTPCodes::CREATED, null, "Ressource added");
                    } else {
                        $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, ["Server failed to execute request"], "Request failed");
                    }
                } else {
                    $this->send(HTTPCodes::NO_CONTENT, null, null);
                }
            } else {
                $this->send(HTTPCodes::NOT_ALLOWED, ["Attempting to add realtion beetween two users"], "Insertion not allowed");
            }
        } 
        else {
                $result = $this->friendModel->add($idUser, $idFriend);
                if ($result) {
                    $this->send(HTTPCodes::CREATED, null, "Ressource added");
                } else {
                    $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, ["Server failed to execute request"], "Request failed");
                }
    }}




public function rejectRequest($idUser, $idFriend){
    if ($this->userModel->getById($idUser) == null || $this->userModel->getById($idFriend) == null) {
        $this->send(HTTPCodes::NOT_FOUND, ["One of the users don't exist"], "Ressource not found");
        return;
    }
    if ($this->friendModel->isFriend($idUser, $idFriend) != null) {
        $this->send(HTTPCodes::BAD_REQUEST, ["Users are already friends"], "Relation exist");
        return;
    }
    if ($idUser == $idFriend) {
        $this->send(HTTPCodes::BAD_REQUEST, ["People cannot be friend with themself "], "Same arguments");
        return;
    }


    if (!$this->user->isDeveloper()) {
        if ($this->user->getId() == $idUser) {

            if ($this->friendRequestModel->isPending($idUser, $idFriend) != null) {
                $result = $this->friendRequestModel->reject($idUser, $idFriend);
                if ($result) {
                    $this->send(HTTPCodes::CREATED, null, "Ressource added");
                } else {
                    $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, null, "Request failed");
                }
            } else {
                $this->send(HTTPCodes::NO_CONTENT, null, null);
            }
        } 
        else {
            $this->send(HTTPCodes::NOT_ALLOWED, null, "Insertion not allowed");
        }



    } else {
        if ($this->friendRequestModel->isPending($idUser, $idFriend) != null) {
            $result = $this->friendRequestModel->reject($idUser, $idFriend);
            if ($result) {
                $this->send(HTTPCodes::CREATED, null, "Ressource added");
            } else {
                $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, null, "Request failed");
            }
        } else {
            $this->send(HTTPCodes::NO_CONTENT, null, null);
        }
    }
}
























    public function remove(int $idUser, $idFriend)
    {
        if ($this->userModel->getById($idUser) == null || $this->userModel->getById($idFriend) == null) {
            $this->send(HTTPCodes::NOT_FOUND, ["One of the users don't exist"], "Ressource not found");
            return;
        }
        if ($this->friendModel->isFriend($idUser, $idFriend) == null) {
            $this->send(HTTPCodes::BAD_REQUEST, ["Users are not friends"], "Relation doesn't exist, cannot be removed");
            return;
        } else {
            $result = $this->friendModel->remove($idUser, $idFriend);
            if ($result) {
                $this->send(HTTPCodes::OK, ["Successfully removed data"], "{$result}");
            } else {
                $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, ["Server failed to execute request"], "Request failed");
            }
        }
    }
}
