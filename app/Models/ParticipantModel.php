<?php

namespace App\Models;

class ParticipantModel extends BaseModel {

    public function getAll(int $idEvent): array {
        return $this->db->table("participant")->getWhere(["idEvent" => $idEvent])->getResultObject();
    }

    public function add(int $idEvent, int $idUser): int {
        $data = [
            "id"      => $this->getMax("participant", "id") + 1,
            "idEvent" => $idEvent,
            "idUser"  => $idUser
        ];

        $this->db->table("participant")->insert($data);

        if (!$this->isLastQuerySuccessful()) {
            return -1;
        }

        return $data["id"];
    }

    public function remove(int $idEvent, int $idUser): bool {
        $this->db->table("participant")->delete(["idEvent" => $idEvent, "idUser" => $idUser]);

        return $this->isLastQuerySuccessful();
    }

    public function isParticipating(int $idEvent, int $idUser): bool {
        $data = $this->db->table("participant")->getWhere(["idUser" => $idUser, "idEvent" => $idEvent])->getFirstRow();

        return $data != null;
    }
}
