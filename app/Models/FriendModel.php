<?php

namespace App\Models;

class FriendModel extends BaseModel {


    public function getAll(int $idUser): array {
        
        $fdb = $this->db->table('friend');
        $dataIdUser1 = $fdb->where('idUser1', $idUser)->get()->getResultArray();
        $dataIdUser2 = $fdb->orWhere('idUser2', $idUser)->get()->getResultArray();
        return array_merge($dataIdUser1, $dataIdUser2);
    }

    public function isFriend(int $idUser, int $idFriend): array | null {
        $array = array('idUser1' => $idUser, 'idUser2' => $idFriend);
        $array2 = array('idUser1' => $idFriend, 'idUser2' => $idUser);
        $data= $this->db->table('friend')->where($array)->orWhere($array2)->get()->getRowArray();
        return $data;
    }
    
}