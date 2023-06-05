<?php

namespace App\Models;


class SearchModel extends BaseModel
{

    public function search($search, $id): array
    {
        return $this->db->table("user u")
        ->select("u.id, u.lastname, u.firstname, u.login, u.pseudo, u.idRole, u.idFoyer, u.pic")
        ->join("blocked b", "(b.idBlocked = u.id AND b.idUser = " . $id . ") OR (b.idUser = u.id AND b.idBlocked = " . $id . ")", "LEFT")
        ->where("(b.idBlocked IS NULL OR b.idUser IS NULL)")
        ->groupStart()
            ->like("u.firstname", $search, "both")
            ->orLike("u.lastname", $search, "both")
            ->orLike("u.pseudo", $search, "both")
            ->orLike("CONCAT(u.lastname, ' ', u.firstname)", $search, "both")
            ->orLike("CONCAT(u.firstname, ' ', u.lastname)", $search, "both")
        ->groupEnd()
        ->limit(5)
        ->get()
        ->getResultObject();
    }
}
