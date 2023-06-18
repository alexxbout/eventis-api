<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder {
  public function run() {
    // user : id lastname	firstname	login	emoji	pseudo showPseudo	password idRole	idFoyer	active pic bio

    $data = [
      [
        "id" => 1,
        "lastname" => "Pierre",
        "firstname" => "Jean",
        "login" => "pjean",
        "emoji" => "alien",
        "pseudo" => "Pierrot",
        "showPseudo" => 1,
        "password" => password_hash("Azert@123", PASSWORD_DEFAULT),
        "idRole" => 3,
        "idFoyer" => 1,
        "active" => 1,
        "pic" => null,
        "bio" => "Je suis un admin"
      ],
      [
        "id" => 2,
        "lastname" => "Raimi",
        "firstname" => "Sam",
        "login" => "sraimi",
        "emoji" => "arts_brush",
        "pseudo" => "Sam",
        "showPseudo" => 1,
        "password" => password_hash("Azert@123", PASSWORD_DEFAULT),
        "idRole" => 0,
        "idFoyer" => 1,
        "active" => 1,
        "pic" => null,
        "bio" => "Je suis un développeur"
      ],
      [
        "id" => 3,
        "lastname" => "Térieur",
        "firstname" => "Alain",
        "login" => "ateur",
        "emoji" => null,
        "pseudo" => "Alain",
        "showPseudo" => 0,
        "password" => password_hash("Azert@123", PASSWORD_DEFAULT),
        "idRole" => 1,
        "idFoyer" => 1,
        "active" => 1,
        "pic" => null,
        "bio" => "Je suis un éducateur"
      ],
      [
        "id" => 4,
        "lastname" => "Martin",
        "firstname" => "Michel",
        "login" => "mmartin",
        "emoji" => "arts_painting",
        "pseudo" => "Michel",
        "showPseudo" => 1,
        "password" => password_hash("Azert@123", PASSWORD_DEFAULT),
        "idRole" => 2,
        "idFoyer" => 1,
        "active" => 1,
        "pic" => null,
        "bio" => "Je suis un utilisateur"
      ],
      [
        "id" => 5,
        "lastname" => "Dupont",
        "firstname" => "Jean",
        "login" => "jdupont",
        "emoji" => "arts_painting",
        "pseudo" => "Jean",
        "showPseudo" => 1,
        "password" => password_hash("Azert@123", PASSWORD_DEFAULT),
        "idRole" => 2,
        "idFoyer" => 2,
        "active" => 1,
        "pic" => null,
        "bio" => "Je suis un utilisateur"
      ]
    ];

    $this->db->table("user")->insertBatch($data);
  }
}
