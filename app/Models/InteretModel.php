<?php

namespace App\Models;

class InteretModel extends BaseModel {

    public function getInterestByUser(int $idUser): array {
        return $this->db->table("user_interest")
        ->where("idUser", $idUser)
        ->join('interest', 'interest.id = user_interest.idInterest')
        ->get()
        ->getResultObject();
    }

    public function isInterest(int $idUser, int $idInterest): bool {
        $array = [
            "idUser" => $idUser,
            "idInterest" => $idInterest
        ];

        $data = $this->db->table("user_interest")->where($array)->get()->getRowArray();
        return $data != null;
    }

    public function add(int $idUser, int $idInterest): bool {
        $data = [
            "idUser" => $idUser,
            "idInterest" => $idInterest
        ];

        $this->db->table("user_interest")->insert($data);

        return $this->isLastQuerySuccessful();
    }

    public function remove(int $idUser, int $idInterest): bool {
        $array = ["idUser" => $idUser, "idInterest" => $idInterest];
        $this->db->table("user_interest")->where($array)->delete();

        return $this->isLastQuerySuccessful();
    }

    public function getAll(): array {
        return $this->db->table("interest")->get()->getResultObject();
    }
}

