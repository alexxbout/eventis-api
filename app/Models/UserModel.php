<?php

namespace App\Models;

class UserModel extends BaseModel {

    public function getAll(): array|null {
        return $this->db->table("user")->get()->getResultArray();
    }

    public function getById(int $id): array|null {
        return $this->db->table("user")->getWhere(["id" => $id])->getRowArray();
    }

    public function getByIdFoyer(int $idFoyer): array|null {
        return $this->db->table("user")->getWhere(["idFoyer" => $idFoyer])->getResultArray();
    }

    public function getByIdRole(int $idRole): array|null {
        return $this->db->table("user")->getWhere(["idRole" => $idRole])->getResultArray();
    }

    public function getByIdRef(int $idRef): array|null {
        return $this->db->table("user")->getWhere(["idRef" => $idRef])->getResultArray();
    }

    public function getByLogin(string $login): array|null {
        return $this->db->table("user")->getWhere(["login" => $login])->getRowArray();
    }

    public function add(string $lastname, string $firstname, string $login, string $password, int $idRole, int $idFoyer): int {
        $data = [
            "id"        => $this->getMax("user", "id") + 1,
            "lastname"  => $lastname,
            "firstname" => $firstname,
            "login"     => $login,
            "password"  => $password,
            "idRole"    => $idRole,
            "idFoyer"   => $idFoyer
        ];

        $this->db->table("user")->insert($data);
        return $data["id"];
    }

    public function updateData(array $data): void {
        $this->db->table("user")->update($data, ["id" => $data["id"]]);
    }

    public function updateLastLogin(int $id): void {
        $this->db->table("user")->update(["lastLogin" => date("Y-m-d H:i:s")], ["id" => $id]);
    }

    public function updateLastLogout(int $id): void {
        $this->db->table("user")->update(["lastLogout" => date("Y-m-d H:i:s")], ["id" => $id]);
    }

    public function updatePassword(int $id, string $password): void {
        $this->db->table("user")->update(["password" => $password], ["id" => $id]);
    }
}
