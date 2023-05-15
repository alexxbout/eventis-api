<?php

namespace App\Models;

use stdClass;

class RoleModel extends BaseModel {

   /* public function getAll(int $id): Array {
        return $this->db->table("role")->getWhere(["id" => $id])->getResultObject();
    }*/

    public function getAll(): Array {
        return $this->db->table("role")->get()->getResultObject();
    }
}



       
        
    


