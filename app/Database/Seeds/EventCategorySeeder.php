<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EventCategorySeeder extends Seeder {
  public function run() {
    // event_category : id name emoji

    $data = [
      [
        "id" => 1,
        "name" => "Concert",
        "emoji" => "music_guitare"
      ],
      [
        "id" => 2,
        "name" => "Sortie sportive",
        "emoji" => "sport_football"
      ],
      [
        "id" => 3,
        "name" => "Jeux vidéos",
        "emoji" => "video_games_joystick"
      ],
      [
        "id" => 4,
        "name" => "Sortie cinéma",
        "emoji" => "popcorn"
      ],
      [
        "id" => 5,
        "name" => "Sortie nature",
        "emoji" => "nature_tree"
      ],
      [
        "id" => 6,
        "name" => "Jeux de société",
        "emoji" => "board_games_dice"
      ],
      [
        "id" => 7,
        "name" => "Soirées rencontres",
        "emoji" => "party_face"
      ],
      [
        "id" => 8,
        "name" => "Sortie boite de nuit",
        "emoji" => "disco_ball"
      ]
    ];

    $this->db->table("event_category")->insertBatch($data);
  }
}
