<?php

namespace App\Models;

class FriendRequestModel extends BaseModel {

    public function askFriend(int $idUser, int $idFriend): bool {
        $data = [
            "id"          => $this->getMax("friend_request", "id") + 1,
            "idRequester" => $idUser,
            "idRequested" => $idFriend,
        ];

        $this->db->table("friend_request")->insert($data);

        return $this->isLastQuerySuccessful();
    }

    public function isPending(int $idRequeted, int $idRequester): array | null {
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

        return $data;
    }

    public function remove(int $idRequester, int $idRequested): bool {
        $array = [
            "idRequester" => $idRequester,
            "idRequested" => $idRequested
        ];

        $array2 = [
            "idRequester" => $idRequested,
            "idRequested" => $idRequester
        ];

        $this->db->table("friend_request")->groupStart()
            ->where($array)
            ->groupEnd()
            ->orGroupStart()
            ->where($array2)
            ->groupEnd()
            ->delete();

        return $this->isLastQuerySuccessful();
    }

    public function isNotRequester($idRequested, $idRequester): bool {
        $array = ["idRequester" => $idRequester, "idRequested" => $idRequested];
        $data = $this->db->table("friend_request")->where($array)->get()->getRowArray();

        return $data != null;
    }
}
