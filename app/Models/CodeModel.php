<?php

namespace App\Models;

class CodeModel extends BaseModel {

    public function getAll(): array {
        return $this->db->table("code")->get()->getResultObject();
    }

    public function getAllByFoyer(int $idFoyer): array {
        return $this->db->table("code")->getWhere(["idFoyer" => $idFoyer])->getResultObject();
    }

    public function getByCode(string $code): object|null {
        return $this->db->table("code")->getWhere(["code" => $code])->getFirstRow();
    }

    public function checkExists(string $code): bool {
        return $this->getByCode($code) != null;
    }

    public function setUsed(int $id): bool {
        $this->db->table("code")->update(["used" => 1], ["id" => $id]);

        return $this->isLastQuerySuccessful();
    }

    public function add(string $code, int $idFoyer, int $idCreator, int $idRole, string $expire): int {
        $data = [
            "id"        => $this->getMax("code", "id") + 1,
            "code"      => $code,
            "idFoyer"   => $idFoyer,
            "expire"    => $expire,
            "createdBy" => $idCreator,
            "idRole"    => $idRole
        ];

        $this->db->table("code")->insert($data);

        if ($this->isLastQuerySuccessful()) {
            return $data["id"];
        } else {
            return -1;
        }
    }

    public function isValid(int $idCode): bool {
        $data = $this->getById($idCode);

        return $data != null && $data->expire > date("Y-m-d H:i:s") && $data->used == 0;
    }

    private function getById(int $id): object|null {
        return $this->db->table("code")->getWhere(["id" => $id])->getFirstRow();
    }
}
