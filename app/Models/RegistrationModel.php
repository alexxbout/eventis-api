<?php

namespace App\Models;

class RegistrationModel extends BaseModel {

    public function getAll(): array {
        return $this->db->table("registration")->orderBy("at", "DESC")->get()->getResultArray();
    }

    public function add(int $idCode, int $idUser): bool {
        $data = [
            "id"     => $this->getMax("registration", "id") + 1,
            "idCode" => $idCode,
            "idUser" => $idUser
        ];

        return $this->db->table("registration")->insert($data);
    }
}
