<?php

namespace App\Models;

use CodeIgniter\Database\RawSql;
use PHPUnit\Framework\MockObject\ReturnValueNotConfiguredException;

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

    public function remove(int $idRequester, int $idRequested)
    {
        $array = ["idRequester" => $idRequester, "idRequested" => $idRequested];
        $array2 = ["idRequester" => $idRequested, "idRequested" => $idRequester];
        $this->db->table("friendrequest")->groupStart()
            ->where($array)
            ->groupEnd()
            ->orGroupStart()
            ->where($array2)
            ->groupEnd()
            ->delete();
        return $this->db->affectedRows() > 0;
    }

    public function isNotRequester($idRequested, $idRequester): bool
    {
        $db = $this->db->table("friendrequest");
        $array = ["idRequester" => $idRequester, "idRequested" => $idRequested];
        $data = $db->where($array)->get()->getRowArray();
        return $data != null;
    }

    // fonction pour annuler sa propre demande d'ami ex: cancelRequest
}
