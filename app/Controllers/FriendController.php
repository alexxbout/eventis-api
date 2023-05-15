<?php

namespace App\Controllers;

use App\Utils\HTTPCodes;

class FriendController extends BaseController
{
    private $friendModel;

    public function __construct()
    {
        $this->friendModel = new \App\Models\FriendModel();
    }



    public function getAll(): void
    {
        $idUser = $this->request->{'data'}->id;
        $data = $this->friendModel->getAll($idUser);
        if (empty($data)) {
            $this->send(HTTPCodes::NO_CONTENT, ["Nobody has any friends"]);
        } else {
            $this->send(HTTPCodes::OK, $data);
        }
    }

    public function isFriend(int $idFriend): void
    {

        $idUser = $this->request->{'data'}->id;

        if ($idUser != $idFriend) {
            $data = $this->friendModel->isFriend($idUser, $idFriend);

            $this->send(HTTPCodes::OK, ["data" => $data == null]);
        } else {
            $err = ["message" => "Same arguments", "data" => "Cannot ask if someone is friend with himself"];
            $this->send(HTTPCodes::BAD_REQUEST, $err);
        }
    }

}
