<?php

namespace App\Models;

class EventModel extends BaseModel {

    public function getAll(): array|null {
        return $this->db->table("event")->get()->getResultArray();
    }

    public function getAllNotCanceled(): array|null {
        return $this->db->table("event")->getWhere(["canceled" => 0])->getResultArray();
    }

    public function getById(int $id): array|null {
        return $this->db->table("event")->getWhere(["id" => $id])->getRowArray();
    }

    public function getIdFoyerByIdEvent(int $idEvent): int {
        $result = $this->db->table("event")->select("idFoyer")->getWhere(["id" => $idEvent])->getRow();
        return $result == null ? -1 : $result;
    }

    public function getByIdNotCanceled(int $id): array|null {
        return $this->db->table("event")->getWhere(["id" => $id, "canceled" => 0])->getRowArray();
    }

    public function getByIdCanceled(int $id): array|null {
        return $this->db->table("event")->getWhere(["id" => $id, "canceled" => 1])->getRowArray();
    }

    public function getByZip(string $zip): array|null {
        return $this->db->table("event")->getWhere(["zip" => $zip])->getResultArray();
    }

    public function getByZipNotCanceled(string $zip): array|null {
        return $this->db->table("event")->getWhere(["zip" => $zip, "canceled" => 0])->getResultArray();
    }

    /**
     * SELECT * from event 
     * JOIN 
     */

    // public function getByIdFoyer(int $id): array|null{
    //     return $this->db->table("event")->getWhere(["zip" => $zip, "canceled" => 0])->getResultArray();;
    // }

    public function cancel(int $id, string $reason): void {
        $this->db->table("event")->update(["canceled" => true, "reason" => $reason], ["id" => $id]);
    }

    public function uncancel(int $id): void {
        $this->db->table("event")->update(["canceled" => false, "reason" => null], ["id" => $id]);
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
