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
        if ($exist==null){ $this->send(HTTPCodes::NOT_FOUND);}
        else { $this->send(200, $exist); }
    }

    public function add(int $idUser, int $idBlocked): void {
      
        if ($idUser == $idBlocked) {$this->send(HTTPCodes::BAD_REQUEST); return ;}

        $isInTable =  $this-> blockedModel-> isBlocked($idUser,$idBlocked);

        if (!$isInTable) {
            if($this->friendModel->isFriend($idUser,$idBlocked)){
                $this->friendModel->remove($idUser,$idBlocked);
            }
            $this-> blockedModel-> add($idUser, $idBlocked);
            $this->send(HTTPCodes::OK);
        } else {
            $this->send(HTTPCodes::BAD_REQUEST);
        }     
    }

    public function remove(int $idUser,int $idBlocked): void {
        
        if ($idUser == $idBlocked) {$this->send(HTTPCodes::BAD_REQUEST); return ;}

        $isInTable =  $this-> blockedModel-> isBlocked($idUser,$idBlocked);

        if ($isInTable) {
            
            $this-> blockedModel-> remove($idUser, $idBlocked);
            $this->send(HTTPCodes::OK);
        } else {
            $this->send(HTTPCodes::NOT_FOUND);
        }     
    }


}
