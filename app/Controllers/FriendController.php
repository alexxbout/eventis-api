<?php

namespace App\Controllers;
use App\Utils\HTTPCodes;

use function PHPUnit\Framework\isEmpty;

class FriendController extends BaseController {
    private $friendModel;

    public function __construct() {
        $this->friendModel = new \App\Models\FriendModel();
    }



    public function getAll(int $idUser): void {
        $data = $this->friendModel->getAll($idUser);
        if(empty($data)){
             $this->send(HTTPCodes::NO_CONTENT,"Bruh");
        }
        else{
            $this->send(HTTPCodes::OK,$data);
        }
    }

    public function isFriend(int $idUser, int $idFriend): void {
        $this->send(200, $this->friendModel->isFriend($idUser, $idFriend));
    }
}
