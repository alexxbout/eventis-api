<?php

namespace App\Models;

use CodeIgniter\Database\RawSql;
use DateInterval;
use DateTime;

class EventModel extends BaseModel {

    public function getAll(): array {
        return $this->db->table("event")->get()->getResultObject();
    }

    public function getAllTypes(): array {
        return $this->db->table("event_categorie")->join("emoji", "interest.id = event_categorie.idEmoji")->get()->getResultObject();
    }

    public function getAllNotCanceled(): array {
        return $this->db->table("event")->getWhere(["canceled" => 0])->getResultObject();
    }

    public function getById(int $id): object|null {
        return $this->db
            ->table("event")
            ->select("event.*, emoji.code")
            ->join("event_categorie", "event_categorie.id = event.idCategorie")
            ->join("emoji", "emoji.id = event_categorie.idEmoji")
            ->getWhere(["event.id" => $id])
            ->getRowObject();
    }

    public function getIdFoyerByIdEvent(int $idEvent): int {
        $result = $this->db->table("event")->select("idFoyer")->getWhere(["id" => $idEvent])->getRow();
        return $result == null ? -1 : $result;
    }

    public function getByIdNotCanceled(int $id): object |  null {
        return $this->db
            ->table("event")
            ->select("event.*, emoji.code")
            ->join("event_categorie", "event_categorie.id = event.idCategorie")
            ->join("emoji", "emoji.id = event_categorie.idEmoji")
            ->getWhere(["event.id" => $id, "canceled" => 0])
            ->getRowObject();
    }

    public function getByIdCanceled(int $id): array {
        return $this->db
            ->table("event")
            ->select("event.*, emoji.code")
            ->join("event_categorie", "event_categorie.id = event.idCategorie")
            ->join("emoji", "emoji.id = event_categorie.idEmoji")
            ->getWhere(["event.id" => $id, "canceled" => 1])
            ->getResultObject();
    }

    public function getByZip(string $zip): array {
        $date = new DateTime();

        $date->modify("-7 day");

        $sql = "zip = '$zip' AND canceled = 1 AND start >= '" . $date->format("Y-m-d H:i:s") . "'";

        return $this->db->table("event")
            ->orderBy("start", "ASC")
            ->select("event.*, emoji.code")
            ->join("event_categorie", "event_categorie.id = event.idCategorie")
            ->join("emoji", "emoji.id = event_categorie.idEmoji")
            ->getWhere(new RawSql($sql))
            ->getResultObject();
    }

    public function getByZipNotCanceled(string $zip): array {
        $date = new DateTime();

        $date->modify("-7 day");

        $sql = "zip = '$zip' AND canceled = 0 AND start >= '" . $date->format("Y-m-d H:i:s") . "'";

        return $this->db->table("event")
            ->orderBy("start", "ASC")
            ->select("event.*, emoji.code")
            ->join("event_categorie", "event_categorie.id = event.idCategorie")
            ->join("emoji", "emoji.id = event_categorie.idEmoji")
            ->getWhere(new RawSql($sql))
            ->getResultObject();
    }

    public function cancel(int $id, string $reason): bool {
        $this->db->table("event")->update(["canceled" => true, "reason" => $reason], ["id" => $id]);

        return $this->isLastQuerySuccessfull();
    }

    public function uncancel(int $id): bool {
        $this->db->table("event")->update(["canceled" => false, "reason" => null], ["id" => $id]);

        return $this->isLastQuerySuccessfull();
    }

    public function updateData(int $id, object $data): bool {
        $this->db->table("event")->update($data, ["id" => $id]);

        return $this->isLastQuerySuccessfull();
    }

    public function add(object $data): int {
        $data->id = $this->getMax("event", "id") + 1;
        $this->db->table("event")->insert($data);
        $this->db->insertID();

        if ($this->isLastQuerySuccessfull()) {
            return $data->id;
        } else {
            return -1;
        }
    }

    public function addImage(int $id, string $image): bool {
        $this->db->table("event")->update(["pic" => $image], ["id" => $id]);

        return $this->isLastQuerySuccessfull();
    }

    public function getImage(int $id): string | null {
        $result = $this->db->table("event")->select("pic")->getWhere(["id" => $id])->getRow();
        return $result == null ? null : $result->pic;
    }
}
