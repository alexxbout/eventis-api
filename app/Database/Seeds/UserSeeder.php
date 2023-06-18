<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder {
  public function run() {
    // user : id	lastname	firstname	login	emoji	pseudo	showPseudo	password	idRole	idFoyer	active	pic	bio

    $data = [[]];

    $this->db->table("user")->insertBatch($data);
  }
}
