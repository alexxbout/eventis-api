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

        if($idUser != $idFriend){
            $data = $this->friendModel->isFriend($idUser, $idFriend);

            if($data == null){
                $this->send(HTTPCodes::OK,[0]);
            }elseif(empty($data)){
                $this->send(HTTPCodes::NO_CONTENT,[0]);
            }
            else{
                $this->send(HTTPCodes::OK,[1]);
            }
        }else{
            $err = ["message" => "Same arguments","data" => "Cannot ask if someone is friend with himself"];
            $this->send(HTTPCodes::BAD_REQUEST,$err);
        }

        
    }
}
