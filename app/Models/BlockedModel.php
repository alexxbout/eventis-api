<?php

namespace App\Models;

use stdClass;

class BlockedModel extends BaseModel {

    //groupes des bloques par un utilisateur
    public function getAll(int $idUser): array {
        return $this->db->table("blocked")->getWhere(["idUser" => $idUser])->getResultObject();

    }

    //un tableau correspondant si idUser est dans la table blocked null sinon
    public function isBlocked(int $idUser, int $idBlocked): bool {
        $result = $this->db->table("blocked")->getWhere(["idUser" => $idUser,"idBlocked" => $idBlocked])->getResultObject();
        return $result!=null ;
    }

    public function add(int $idUser, int $idBlocked ): void {
        $data = [
            "id"         => $this->getMax("blocked", "id") + 1,
            "idUser"     => $idUser,
            "idBlocked"  => $idBlocked
        ];

        $this->db->table("blocked")->insert($data);
        
    }
    public function remove(int $idUser, int $idBlocked): void {
       $this->db->table("blocked")->delete(["idBlocked" => $idBlocked,"idUser" => $idUser]);
    }

}
