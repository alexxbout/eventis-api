<?php

namespace App\Models;

class NotificationModel extends BaseModel {
    public function getAll(int $idUser): array {
        return $this->db->table("notification")->getWhere(["idUser" => $idUser])->getResultObject();
    }

    public function getEventNotifications($idUser): array {
        return $this->db->table("notification")->getWhere(["idUser" => $idUser, "idNotifType" => 1])->getResultObject();
    }

    public function getFriendRequestNotifications($idUser): array {
        return $this->db->table("notification")->getWhere(["idUser" => $idUser, "idNotifType" => 0])->getResultObject();
    }

    public function addFriendRequestNotification(int $idUser, int $idFriend): bool {
        $id = $this->getMax("notification", "id") + 1;
        $time = date("Y-m-d H:i:s");
        $data = [
            "id"          => $id,
            "idUser"      => $idUser,
            "idAlt"       => $idFriend,
            "idNotifType" => 0,
            "created"     => $time
        ];
        $this->db->table("notification")->insert($data);
        $this->db->insertID();
        
        return $this->isLastQuerySuccessfull();
    }

    // Peut-être à supprimer pour garder qu'une seule fonction générique
    public function addNewEventNotification(int $idUser, int $idEvent): bool {
        $id = $this->getMax("notification", "id") + 1;
        $time = date("Y-m-d H:i:s");
        $data = [
            "id"          => $id,
            "idUser"      => $idUser,
            "idAlt"       => $idEvent,
            "idNotifType" => 1,
            "created"     => $time
        ];
        $this->db->table("notification")->insert($data);
        $this->db->insertID();

        return $this->isLastQuerySuccessfull();
    }

    public function remove(int $idUser, int $idAlt, int $idNotifType): bool{
        $this->db->table("notification")->delete(["idUser" => $idUser, "idAlt" => $idAlt, "idNotifType" => $idNotifType]);
        return $this->isLastQuerySuccessfull();
    }

    public function removeNotification(int $id): bool{
        $this->db->table("notification")->delete(["id" => $id]);
        return $this->isLastQuerySuccessfull();
    }
}