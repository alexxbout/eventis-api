<?php

namespace App\Models;

class EventModel extends BaseModel {

    public function getAll(): array|null {
        return $this->db->table("event")->get()->getResultArray();
    }

    public function getById(int $id): array|null {
        return $this->db->table("event")->getWhere(["id" => $id])->getRowArray();
    }

    public function getByZip(string $zip): array|null {
        return $this->db->table("event")->getWhere(["zip" => $zip])->getResultArray();
    }

    public function cancel(int $id, string $reason): bool {
        return $this->db->table("event")->update(["canceled" => true, "reason" => $reason], ["id" => $id]);
    }

    public function updateData(array $data): bool {
        return $this->db->table("event")->update($data, ["id" => $data["id"]]);
    }

    public function add(array $data): bool {
        $data["id"] = $this->getMax("event", "id") + 1;
        return $this->db->table("event")->insert($data);
    }

    public function addImage(int $id, string $image): bool {
        return $this->db->table("event")->update(["pic" => $image], ["id" => $id]);
    }
}
