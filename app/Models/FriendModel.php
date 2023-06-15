<?php

namespace App\Models;

class FriendModel extends BaseModel {

    public function getAll(int $idUser): array {
        $dataIdUser1 = $this->db->table("friend")
            ->select("user.*")
            ->join("user", "user.id = friend.idUser2")
            ->where("idUser1", $idUser)
            ->get()
            ->getResultObject();

        $dataIdUser2 = $this->db->table("friend")
            ->select("user.*")
            ->join("user", "user.id = friend.idUser1")
            ->orWhere("idUser2", $idUser)
            ->get()
            ->getResultObject();

        return array_merge($dataIdUser1, $dataIdUser2);
    }

    public function isFriend(int $idUser, int $idFriend): bool {
        $array = [
            "idUser1" => $idUser,
            "idUser2" => $idFriend
        ];

        $array2 = [
            "idUser1" => $idFriend,
            "idUser2" => $idUser
        ];

        $data = $this->db
            ->table("friend")
            ->groupStart()
            ->where($array)
            ->groupEnd()
            ->orGroupStart()
            ->where($array2)
            ->groupEnd()
            ->get()->getRowArray();

        return $data != null;
    }

    public function askFriend(int $idUser, int $idFriend): int {
        $data = [
            "id"          => $this->getMax("friend_request", "id") + 1,
            "idRequester" => $idUser,
            "idRequested" => $idFriend,
        ];

        $this->db->table("friend_request")->insert($data);

        if ($this->isLastQuerySuccessful()) {
            return $data["id"];
        } else {
            return -1;
        }
    }

    public function isPending(int $idRequeted, int $idRequester): bool {
        $array = [
            "idRequester" => $idRequeted,
            "idRequested" => $idRequester
        ];

        $array2 = [
            "idRequester" => $idRequester,
            "idRequested" => $idRequeted
        ];

        $data = $this->db
            ->table("friend_request")
            ->groupStart()
            ->where($array)
            ->groupEnd()
            ->orGroupStart()
            ->where($array2)
            ->groupEnd()
            ->get()->getRowArray();

        return $data != null;
    }

    public function add(int $idUser, int $idFriend): bool {
        $now = date("Y-m-d H:i:s");
        $data = [
            "idUser1" => $idUser,
            "idUser2" => $idFriend,
            "since" => $now
        ];

        $this->db->table("friend")->insert($data);

        return $this->isLastQuerySuccessful();
    }

    public function remove(int $idUser, int $idFriend): bool {
        $array = ["idUser1" => $idUser, "idUser2" => $idFriend];
        $array2 = ["idUser1" => $idFriend, "idUser2" => $idUser];
        $this->db
            ->table("friend")
            ->groupStart()
            ->where($array)
            ->groupEnd()
            ->orGroupStart()
            ->where($array2)
            ->groupEnd()
            ->delete();

        return $this->isLastQuerySuccessful();
    }
}
