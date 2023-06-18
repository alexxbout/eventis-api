<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FoyerSeeder extends Seeder {
  public function run() {
    // foyer : id	siret	city zip address

    $data = [
      [
        "id" => 1,
        "siret" => "12345678912345",
        "city" => "Rennes",
        "zip" => "35000",
        "address" => "1 rue de la paix"
      ],
      [
        "id" => 2,
        "siret" => "12345678912346",
        "city" => "La Gacilly",
        "zip" => "56200",
        "address" => "2 rue de la paix"
      ],
      [
        "id" => 3,
        "siret" => "12345678912347",
        "city" => "Nantes",
        "zip" => "44000",
        "address" => "3 rue de la paix"
      ]
    ];

    $this->db->table("foyer")->insertBatch($data);
  }
}
