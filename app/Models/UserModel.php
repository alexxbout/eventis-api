<?php

namespace App\Models;

class UserModel extends BaseModel {

    public function getAll(): array {
        return $this->db->table('user')->get()->getResultArray();
    }

    public function getById(int $id): array {
        return $this->db->table('user')->getWhere(['id' => $id])->getRowArray();
    }

    public function getByIdFoyer(int $idFoyer): array {
        return $this->db->table('user')->getWhere(['idFoyer' => $idFoyer])->getResultArray();
    }

    public function getByIdRole(int $idRole): array {
        return $this->db->table('user')->getWhere(['idRole' => $idRole])->getResultArray();
    }

    public function getByIdRef(int $idRef): array {
        return $this->db->table('user')->getWhere(['idRef' => $idRef])->getResultArray();
    }

    public function add(array $data): void {
        $data['id'] = $this->getMax("user", "id") + 1;
        $this->db->table('user')->insert($data);
    }

    public function updateData(array $data): void {
        $this->db->table('user')->update($data, ['id' => $data['id']]);
    }

    public function updateLastLogin(int $id): void {
        $this->db->table('user')->update(['lastLogin' => date('Y-m-d H:i:s')], ['id' => $id]);
    }

    public function updateLastLogout(int $id): void {
        $this->db->table('user')->update(['lastLogout' => date('Y-m-d H:i:s')], ['id' => $id]);
    }
}
