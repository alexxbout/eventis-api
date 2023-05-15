<?php

namespace App\Models;

use CodeIgniter\Database\RawSql;

class FriendRequestModel extends BaseModel
{

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

    public function reject(int $idRequester, int $idRequested): bool| string
    {
        $result = $this->db->table("friendrequest")->delete(["idRequester" => $idRequester, "idRequested" => $idRequested]);

        if (!$result) {
            return $this->db->table("friendrequest")->delete(["idRequester" => $idRequested, "idRequested" => $idRequester]);
        }
        
        return $result;
    }

    // fonction pour annuler sa propre demande d'ami ex: cancelRequest
}
