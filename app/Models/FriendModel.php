<?php

namespace App\Models;

class FriendModel extends BaseModel
{
    public function getAll(int $idUser): array |null
    {

        $fdb = $this->db->table("friend");
        $dataIdUser1 = $fdb->where("idUser1", $idUser)->get()->getResultArray();
        $dataIdUser2 = $fdb->orWhere("idUser2", $idUser)->get()->getResultArray();
        return array_merge($dataIdUser1, $dataIdUser2);
    }

    public function isFriend(int $idUser, int $idFriend): array | null
    {
        $db = $this->db->table("friend");
        $array = ["idUser1" => $idUser, "idUser2" => $idFriend];
        $array2 = ["idUser1" => $idFriend, "idUser2" => $idUser];
        $data = $db
            ->groupStart()
            ->where($array)
            ->groupEnd()
            ->orGroupStart()
            ->where($array2)
            ->groupEnd()
            ->get()->getRowArray();
        return $data;
    }

    public function askFriend(int $idUser, int $idFriend): bool
    {
        $db = $this->db->table("friendrequest");
        $data = [
            "id"          => $this->getMax("friendrequest", "id") + 1,
            "idRequester" => $idUser,
            "idRequested" => $idFriend,
        ];
        return $db->insert($data);
    }

    public function isPending(int $idRequeted, int $idRequester): array | null
    {
        $db = $this->db->table("friendrequest");
        $array = ["idRequester" => $idRequeted, "idRequested" => $idRequester];
        $array2 = ["idRequester" => $idRequester, "idRequested" => $idRequeted];
        $data = $db
            ->groupStart()
            ->where($array)
            ->groupEnd()
            ->orGroupStart()
            ->where($array2)
            ->groupEnd()
            ->get()->getRowArray();
        return $data;
    }

    public function add(int $idUser, int $idFriend): bool
    {
        $db = $this->db->table("friend");

        $data = [
            "idUser1" => $idUser,
            "idUser2" => $idFriend,
        ];
        return $db->insert($data);
    }

    public function remove(int $idUser, int $idFriend): bool
    {
        $db = $this->db->table("friend");
        $data = [
            "idUser1" => $idUser,
            "idUser2" => $idFriend,
        ];
        return $db->delete($data);
    }
}
