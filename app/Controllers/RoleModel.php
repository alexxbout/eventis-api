<?php

namespace App\Models;

use stdClass;

class RoleModel extends BaseModel {

    //groupes des bloques par un utilisateur
    public function getById(int $id): Array {
        return $this->db->table("role")->getWhere(["id" => $id])->getResultObject();
    }
}



       
        
    


