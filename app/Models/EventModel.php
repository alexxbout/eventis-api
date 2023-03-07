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

    public function cancel(int $id, string $reason): void {
        $this->db->table("event")->update(["canceled" => true, "reason" => $reason], ["id" => $id]);
    }

    public function updateData(array $data): void {
        $this->db->table("event")->update($data, ["id" => $data["id"]]);
    }

    public function add(array $data): void {
        $data["id"] = $this->getMax("event", "id") + 1;
        $this->db->table("event")->insert($data);
        $this->db->insertID();
    }

    public function addImage(int $id, string $image): void {
        $this->db->table("event")->update(["pic" => $image], ["id" => $id]);
    }
}
