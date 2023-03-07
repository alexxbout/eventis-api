<?php

namespace App\Models;

class FriendModel extends BaseModel {


    public function getAll(int $idUser): array {
        
        $fdb = $this->db->table('friend');
        $dataIdUser1 = $fdb->where('idUser1', $idUser)->get()->getResultArray();
        $dataIdUser2 = $fdb->orWhere('idUser2', $idUser)->get()->getResultArray();
        $combined = fusionner_tableaux($dataIdUser1, $dataIdUser2);
        return $combined;
    }

    public function isFriend(int $idUser, int $idFriend): bool {
        $array = array('idUser1' => $idUser, 'idUser2' => $idFriend);
        $data= $this->db->table('friend')->getWhere($array)->getRowArray();
        return $data;
    }
    
}



function fusionner_tableaux($tableau1, $tableau2) {
    for($i=0; $i<count($tableau2); $i++){
        array_push($tableau1, $tableau2[$i]);      
    }
    return $tableau1;
  }