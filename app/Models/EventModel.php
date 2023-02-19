<?php

namespace App\Models;

class EventModel extends BaseModel {

    public function getAll(): array {
        return $this->db->table('event')->get()->getResultArray();
    }

    public function getById(int $id): array {
        return $this->db->table('event')->getWhere(['id' => $id])->getRowArray();
    }

    public function getByZip(string $zip): array {
        return $this->db->table('event')->getWhere(['zip' => $zip])->getResultArray();
    }

    public function cancel(int $idEvent, string $reason): void {
        $this->db->table('event')->update(['canceled' => true, 'reason' => $reason], ['id' => $idEvent]);
    }

    public function updateData(array $data): void {
        $this->db->table('event')->update($data, ['id' => $data['id']]);
    }

    public function add(array $data): int {
        $data['id'] = $this->getMax("event", "id") + 1;
        $this->db->table('event')->insert($data);
        return $this->db->insertID();
    }
}
