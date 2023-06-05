<?php

namespace App\Models;


class SearchModel extends BaseModel
{

    public function search($search): array{

        return $this->db->table("user")
        ->select("id, lastname,firstname,login,pseudo,idRole,idFoyer,pic")
        ->like("firstname", $search, "after")
        ->orLike("lastname", $search, "both")
        ->orLike("pseudo", $search, "after")
        ->orLike("CONCAT(lastname, ' ', firstname)", $search, "after")
        ->orLike("CONCAT(firstname, ' ', lastname)", $search, "after")->get()->getResultObject();
    }
}
