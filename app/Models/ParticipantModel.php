<?php

namespace App\Models;

class ParticipantModel extends BaseModel
{

    public function getAll(int $idEvent): array
    {
        return $this->db->table("participant")->getWhere(["idEvent" => $idEvent])->getResultObject();
    }

    public function add(int $idEvent, int $idUser): int
    {
        $data = [
            "id"      => $this->getMax("participant", "id") + 1,
            "idEvent" => $idEvent,
            "idUser"  => $idUser
        ];

        $this->db->table("participant")->insert($data);

        if (!$this->isLastQuerySuccessfull()) {
            return -1;
        }

        return $data["id"];
    }

    public function remove(int $idEvent, int $idUser): bool
    {
        $this->db->table("participant")->delete(["idEvent" => $idEvent, "idUser" => $idUser]);

        return $this->isLastQuerySuccessfull();
    }

    public function isParticipating(int $idUser, int $idEvent): bool
    {
        $data = $this->db->table("participant")->getWhere(["idUser" => $idUser, "idEvent" => $idEvent])->getFirstRow();

        return $data != null;
    }
}
