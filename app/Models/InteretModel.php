<?php

namespace App\Models;

class InteretModel extends BaseModel {

    public function getInterestByUser(int $idUser): array {
        return $this->db->table("user_interest")->where("idUser", $idUser)->join('interest', 'interest.id = user_interest.idInterest')->join('emoji', 'emoji.id = interest.emoji')->get()->getResultObject();
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
            "id"          => $this->getMax("user_interest", "id") + 1,
            "idUser" => $idUser,
            "idInterest" => $idInterest
        ];

        $this->db->table("user_interest")->insert($data);

        return $this->isLastQuerySuccessfull();
    }

    public function remove(int $idUser, int $idInterest): bool {
        $array = ["idUser" => $idUser, "idInterest" => $idInterest];
        $this->db->table("user_interest")->where($array)->delete();

        return $this->isLastQuerySuccessfull();
    }

    public function getAll(): array {
        return $this->db->table("interest")->get()->getResultObject();
    }
}

