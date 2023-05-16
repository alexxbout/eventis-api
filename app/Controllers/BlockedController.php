<?php

namespace App\Controllers;

use App\Utils\HTTPCodes;
use App\Models\FriendModel;

class BlockedController extends BaseController {
    private $blockedModel;
    private FriendModel $friendModel;
    
    
    public function __construct() {
        $this->blockedModel = new \App\Models\BlockedModel();
        
    }

    public function getAll(int $idUser): void {
        $exist =  $this->blockedModel->getAll($idUser);
        
        if ($exist == null){ $this->send(HTTPCodes::NO_CONTENT);}
        else { $this->send(HTTPCodes::OK, $exist); }
    }

    public function add(int $idUser, int $idBlocked): void {
      
        if ($idUser == $idBlocked) {$this->send(HTTPCodes::BAD_REQUEST,null,"Cannot block yourself"); return ;}

        $isInTable =  $this-> blockedModel-> isBlocked($idUser,$idBlocked);

        if (!$isInTable) {
            if($this->friendModel->isFriend($idUser,$idBlocked)){
                $this->friendModel->remove($idUser,$idBlocked);
            }
            $this-> blockedModel-> add($idUser, $idBlocked);
            $this->send(HTTPCodes::OK,null,"User blocked");
        } else {
            $this->send(HTTPCodes::BAD_REQUEST,null,"User already blocked");
        }     
    }

    public function remove(int $idUser,int $idBlocked): void {
        
        if ($idUser == $idBlocked) {$this->send(HTTPCodes::BAD_REQUEST,null,"Cannot unblock yourself"); return ;}

        $isInTable =  $this-> blockedModel-> isBlocked($idUser,$idBlocked);

        if ($isInTable && $idUser == $this->user->getId()) {
            
            $this-> blockedModel-> remove($idUser, $idBlocked);
            $this->send(HTTPCodes::OK,null,"User unblocked");
        } else {
            $this->send(HTTPCodes::NOT_FOUND,null,"User was not blocked");
        }     
    }


}
