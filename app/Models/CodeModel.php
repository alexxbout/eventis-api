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
        return $this->db->table("code")->select(["id", "code", "idRole", "idFoyer"])->getWhere(["code" => $code])->getFirstRow();
    }

    public function checkExists(string $code): bool {
        return $this->getByCode($code) != null;
    }

    public function setUsed(int $id): bool {
        return $this->db->table("code")->update(["used" => 1], ["id" => $id]);
    }

    public function add(string $code, int $idFoyer, int $idCreator, int $idRole, string $expire): bool {
        $data = [
            "id"        => $this->getMax("code", "id") + 1,
            "code"      => $code,
            "idFoyer"   => $idFoyer,
            "expire"    => $expire,
            "createdBy" => $idCreator,
            "idRole"    => $idRole
        ];
        return $this->db->table("code")->insert($data);
    }

    public function isValid(int $idCode): bool {
        $data = $this->getById($idCode);
        if ($data == null) {
            return false;
        }

        return $data->expire > date("Y-m-d H:i:s") && $data->used == 0;
    }

    private function getById(int $id): object|null {
        return $this->db->table("code")->getWhere(["id" => $id])->getFirstRow();
    }
}
