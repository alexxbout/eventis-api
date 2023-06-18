<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InterestSeeder extends Seeder {
  public function run() {
    // interest : id	name	emoji	color

    $data = [
      [
        "id" => 1,
        "name" => "Sport",
        "emoji" => "sport_football",
        "color" => "#3697FF"
      ],
      [
        "id" => 2,
        "name" => "Fête",
        "emoji" => "party_face",
        "color" => "#FFB02E"
      ],
      [
        "id" => 3,
        "name" => "Lecture",
        "emoji" => "book_open",
        "color" => "#5DD8FB"
      ],
      [
        "id" => 4,
        "name" => "Jeux vidéo",
        "emoji" => "video_games_joystick",
        "color" => "#FF0000"
      ],
      [
        "id" => 5,
        "name" => "Jeux de société",
        "emoji" => "board_games_card",
        "color" => "#7F00FF"
      ],
      [
        "id" => 6,
        "name" => "Cinéma",
        "emoji" => "popcorn",
        "color" => "#F42165"
      ],
      [
        "id" => 7,
        "name" => "Nature",
        "emoji" => "nature_leaf",
        "color" => "#083B32"
      ],
      [
        "id" => 8,
        "name" => "Cuisine",
        "emoji" => "cooking_sandwich",
        "color" => "#9F7550"
      ],
      [
        "id" => 9,
        "name" => "Musique",
        "emoji" => "music_guitare",
        "color" => "#FF0000"
      ],
      [
        "id" => 10,
        "name" => "Arts",
        "emoji" => "arts_painting",
        "color" => "#FFC0CB"
      ]
    ];

    $this->db->table("interest")->insertBatch($data);
  }
}
