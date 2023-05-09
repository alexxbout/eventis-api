<?php

namespace App\Models;

class RegistrationModel extends BaseModel {

    public function getAll(): array {
        return $this->db->table("registration")->get()->getResultArray();
    }

    public function add(int $idCode, int $idUser): void {
        $data = [
            "id"     => $this->getMax("registration", "id") + 1,
            "idCode" => $idCode,
            "idUser" => $idUser
        ];

        $this->db->table("registration")->insert($data);
    }
}