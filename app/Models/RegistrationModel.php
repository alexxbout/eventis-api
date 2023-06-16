<?php

namespace App\Models;

class RegistrationModel extends BaseModel {

    public function getAll(): array {
        return $this->db->table("registration")->orderBy("at", "DESC")->get()->getResultObject();
    }

    public function add(int $idCode, int $idUser): int {
        $data = [
            "id"     => $this->getMax("registration", "id") + 1,
            "idCode" => $idCode,
            "idUser" => $idUser
        ];

        $this->db->table("registration")->insert($data);

        if ($this->isLastQuerySuccessful()) {
            return $data["id"];
        } else {
            return -1;
        }
    }
}
