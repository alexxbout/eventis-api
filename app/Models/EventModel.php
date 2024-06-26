<?php

namespace App\Models;

use DateTime;

class EventModel extends BaseModel {

    public function getAll(): array {
        return $this->db->table("event")->get()->getResultObject();
    }

    public function getAllTypes(): array {
        return $this->db->table("event_category")
            ->get()->getResultObject();
    }

    public function getAllNC(): array {
        return $this->db->table("event")->getWhere(["canceled" => 0])->getResultObject();
    }

    public function getById(int $id): object|null {
        return $this->db
            ->table("event")
            ->select("event.*, event_category.emoji")
            ->join("event_category", "event_category.id = event.idCategory")
            ->getWhere(["event.id" => $id])
            ->getRowObject();
    }

    public function getIdFoyerByIdEvent(int $idEvent): int {
        $result = $this->db->table("event")->select("idFoyer")->getWhere(["id" => $idEvent])->getRow();
        return $result == null ? -1 : $result->idFoyer;
    }

    public function getByIdNC(int $id): object |  null {
        return $this->db
            ->table("event")
            ->select("event.*, event_category.emoji")
            ->join("event_category", "event_category.id = event.idCategory")
            ->getWhere(["event.id" => $id, "canceled" => 0])
            ->getRowObject();
    }

    public function getByIdCanceled(int $id): object {
        return $this->db
            ->table("event")
            ->select("event.*, event_category.emoji")
            ->join("event_category", "event_category.id = event.idCategory")
            ->getWhere(["event.id" => $id, "canceled" => 1])
            ->getRow();
    }

    public function getByDepartment(string $dpt): array {
        $date = new DateTime();
        $date->modify("-7 day");

        $query = $this->db->table("event")
            ->orderBy("start", "ASC")
            ->select("event.*, event_category.emoji")
            ->join("event_category", "event_category.id = event.idCategory")
            ->where("department", $dpt)
            ->where("start >=", $date->format("Y-m-d H:i:s"));

        $result = $query->get()->getResultObject();

        return $result;
    }

    public function getByDepartmentNC(string $dpt): array {
        $date = new DateTime();
        $date->modify("-7 day");

        $query = $this->db->table("event")
            ->orderBy("start", "ASC")
            ->select("event.*, event_category.emoji")
            ->join("event_category", "event_category.id = event.idCategory")
            ->where("department", $dpt)
            ->where("canceled", 0)
            ->where("start >=", $date->format("Y-m-d H:i:s"));

        $result = $query->get()->getResultObject();

        return $result;
    }

    public function getByDayAndDepartment(string $date, string $dpt): array {
        $query = $this->db->table("event")
            ->orderBy("start", "ASC")
            ->select("event.*, event_category.emoji")
            ->join("event_category", "event_category.id = event.idCategory")
            ->where("department", $dpt)
            ->where("canceled", 0)
            ->where("start", $date);

        $result = $query->get()->getResultObject();

        return $result;
    }

    public function getDistinctDates(int $coveredMonths): array {
        $date = date("Y-m-d");
        $endDate = date("Y-m-d", strtotime("+ $coveredMonths months"));

        return $this->db->table("event")
            ->select("DATE(start) AS start")
            ->distinct()
            ->where("start >=", $date)
            ->where("start <=", $endDate)
            ->orderBy("start", "ASC")
            ->get()->getResultObject();
    }

    public function cancel(int $id, string $reason): bool {
        $this->db->table("event")->update(["canceled" => true, "reason" => $reason], ["id" => $id]);

        return $this->isLastQuerySuccessful();
    }

    public function uncancel(int $id): bool {
        $this->db->table("event")->update(["canceled" => false, "reason" => null], ["id" => $id]);

        return $this->isLastQuerySuccessful();
    }

    public function updateData(int $id, object $data): bool {
        $this->db->table("event")->update($data, ["id" => $id]);

        return $this->isLastQuerySuccessful();
    }

    public function add(object $data): int {
        $data->id = $this->getMax("event", "id") + 1;
        $this->db->table("event")->insert($data);
        $this->db->insertID();

        if ($this->isLastQuerySuccessful()) {
            return $data->id;
        } else {
            return -1;
        }
    }

    public function addImage(int $id, string $image): bool {
        $this->db->table("event")->update(["pic" => $image], ["id" => $id]);

        return $this->isLastQuerySuccessful();
    }

    public function getImage(int $id): string | null {
        $result = $this->db->table("event")->select("pic")->getWhere(["id" => $id])->getRow();
        return $result == null ? null : $result->pic;
    }
}
