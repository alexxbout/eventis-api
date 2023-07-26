<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EventSeeder extends Seeder {
  public function run() {
    // event : id	idFoyer	department address city canceled reason start title idCategory description pic

    $data = [
      [
        "id" => 1,
        "idFoyer" => 1,
        "department" => "35",
        "address" => "1 rue de la paix",
        "city" => "Rennes",
        "canceled" => 0,
        "reason" => null,
        "start" => date("Y-m-d", strtotime("+1 months")),
        "title" => "Fête de la musique",
        "idCategory" => 6,
        "description" => "Venez à cette fête de la musique, on va s'éclater !",
        "pic" => "this/is/a/test/path"
      ],
      [
        "id" => 2,
        "idFoyer" => 1,
        "department" => "35",
        "address" => "1 rue de la paix",
        "city" => "Rennes",
        "canceled" => 0,
        "reason" => null,
        "start" => date("Y-m-d", strtotime("+1 months")),
        "title" => "Sortie cinéma",
        "idCategory" => 4,
        "description" => "Venez voir le dernier film de Marvel !",
        "pic" => null
      ],
      [
        "id" => 3,
        "idFoyer" => 1,
        "department" => "35",
        "address" => "1 rue de la paix",
        "city" => "Rennes",
        "canceled" => 0,
        "reason" => null,
        "start" => date("Y-m-d", strtotime("+3 months")),
        "title" => "Sortie sportive",
        "idCategory" => 2,
        "description" => "Venez faire du sport avec nous !",
        "pic" => null
      ],
      [
        "id" => 4,
        "idFoyer" => 2,
        "department" => "56",
        "address" => "2 rue de la paix",
        "city" => "La Gacilly",
        "canceled" => 0,
        "reason" => null,
        "start" => date("Y-m-d", strtotime("+2 months")),
        "title" => "Soirée rencontre",
        "idCategory" => 7,
        "description" => "Venez rencontrer des gens !",
        "pic" => null
      ],
      [
        "id" => 5,
        "idFoyer" => 3,
        "department" => "44",
        "address" => "3 rue de la paix",
        "city" => "Nantes",
        "canceled" => 1,
        "reason" => "Pas assez de participants",
        "start" => date("Y-m-d", strtotime("+1 months")),
        "title" => "Soirée jeux vidéo",
        "idCategory" => 5,
        "description" => "Venez jouer à des jeux vidéo avec nous !",
        "pic" => null
      ]
    ];

    $this->db->table("event")->insertBatch($data);
  }
}
