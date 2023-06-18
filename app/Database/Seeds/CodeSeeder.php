<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CodeSeeder extends Seeder {
  public function run() {
    // code : id code	idFoyer	expire used	createdBy	idRole

    $data = [
      [
        "id" => 1,
        "code" => "AAAAA",
        "idFoyer" => 1,
        "expire" => date("Y-m-d H:i:s", strtotime("+7 days")),
        "used" => 1,
        "createdBy" => 2,
        "idRole" => 2
      ],
      [
        "id" => 2,
        "code" => "AAAAB",
        "idFoyer" => 1,
        "expire" => date("Y-m-d H:i:s", strtotime("+7 days")),
        "used" => 1,
        "createdBy" => 2,
        "idRole" => 2
      ],
      [
        "id" => 3,
        "code" => "AAAAC",
        "idFoyer" => 1,
        "expire" => date("Y-m-d H:i:s", strtotime("+7 days")),
        "used" => 1,
        "createdBy" => 2,
        "idRole" => 2
      ],
      [
        "id" => 4,
        "code" => "AAAAD",
        "idFoyer" => 1,
        "expire" => date("Y-m-d H:i:s", strtotime("+7 days")),
        "used" => 1,
        "createdBy" => 2,
        "idRole" => 2
      ],
      [
        "id" => 5,
        "code" => "AAAAE",
        "idFoyer" => 1,
        "expire" => date("Y-m-d H:i:s", strtotime("+7 days")),
        "used" => 1,
        "createdBy" => 2,
        "idRole" => 2
      ],
      [
        "id" => 6,
        "code" => "AAAFA",
        "idFoyer" => 1,
        "expire" => date("Y-m-d H:i:s", strtotime("+7 days")),
        "used" => 0,
        "createdBy" => 2,
        "idRole" => 2
      ],
      [
        "id" => 7,
        "code" => "AAAFB",
        "idFoyer" => 1,
        "expire" => date("Y-m-d H:i:s", strtotime("+7 days")),
        "used" => 0,
        "createdBy" => 2,
        "idRole" => 2
      ],
      [
        "id" => 8,
        "code" => "AAAFD",
        "idFoyer" => 1,
        "expire" => date("Y-m-d H:i:s", strtotime("+7 days")),
        "used" => 1,
        "createdBy" => 2,
        "idRole" => 2
      ],
    ];

    $this->db->table("code")->insertBatch($data);
  }
}
