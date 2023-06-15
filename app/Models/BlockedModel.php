<?php

namespace App\Models;


class BlockedModel extends BaseModel {

    public function getAll(int $idUser): array {
        return $this->db->table("blocked")->getWhere(["idUser" => $idUser])->getResultObject();
    }

    public function isBlocked(int $idUser, int $idBlocked): bool {

        $array = ["idUser" => $idUser, "idBlocked" => $idBlocked];
        $array2 = ["idUser" => $idBlocked, "idBlocked" => $idUser];
        $result = $this->db->table("blocked")
            ->groupStart()
            ->where($array)
            ->groupEnd()
            ->orGroupStart()
            ->where($array2)
            ->groupEnd()
            ->get()->getRowArray();

        return $result != null;
    }

    public function add(int $idUser, int $idBlocked): int {
        $data = [
            "id"         => $this->getMax("blocked", "id") + 1,
            "idUser"     => $idUser,
            "idBlocked"  => $idBlocked
        ];

        $this->db->table("blocked")->insert($data);

        if ($this->isLastQuerySuccessful()) {
            return $data["id"];
        } else {
            return -1;
        }
    }

    public function remove(int $idUser, int $idBlocked): int {
        $this->db->table("blocked")->delete(["idBlocked" => $idBlocked, "idUser" => $idUser]);

        return $this->isLastQuerySuccessful();
    }
}
