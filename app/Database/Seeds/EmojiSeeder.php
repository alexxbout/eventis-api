<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EmojiSeeder extends Seeder {
  public function run() {
    // emoji : code

    $data = [
      [
        "code" => "alien"
      ],
      [
        "code" => "arts_brush"
      ],
      [
        "code" => "arts_painting"
      ],
      [
        "code" => "arts_palette"
      ],
      [
        "code" => "balloon"
      ],
      [
        "code" => "baseball"
      ],
      [
        "code" => "basketball"
      ],
      [
        "code" => "beach_umbrella"
      ],
      [
        "code" => "bear"
      ],
      [
        "code" => "beer_mug"
      ],
      [
        "code" => "bird"
      ],
      [
        "code" => "blossom"
      ],
      [
        "code" => "board_games_card"
      ],
      [
        "code" => "board_games_dice"
      ],
      [
        "code" => "book_open"
      ],
      [
        "code" => "bowling"
      ],
      [
        "code" => "butterfly"
      ],
      [
        "code" => "camping"
      ],
      [
        "code" => "cat_face"
      ],
      [
        "code" => "christmas_tree"
      ],
      [
        "code" => "cinema_cam"
      ],
      [
        "code" => "cinema_clap"
      ],
      [
        "code" => "cinema_strip"
      ],
      [
        "code" => "circus_tent"
      ],
      [
        "code" => "cooking_sandwich"
      ],
      [
        "code" => "crown"
      ],
      [
        "code" => "cupcake"
      ],
      [
        "code" => "desert"
      ],
      [
        "code" => "desert_island"
      ],
      [
        "code" => "disco_ball"
      ],
      [
        "code" => "dog_face"
      ],
      [
        "code" => "dolphin"
      ],
      [
        "code" => "dragon_face"
      ],
      [
        "code" => "droplet"
      ],
      [
        "code" => "ferris_wheel"
      ],
      [
        "code" => "fire"
      ],
      [
        "code" => "flamingo",
      ],
      [
        "code" => "flying_saucer"
      ],
      [
        "code" => "four_leaf_clover"
      ],
      [
        "code" => "fox"
      ],
      [
        "code" => "ghost"
      ],
      [
        "code" => "globe"
      ],
      [
        "code" => "grinning_face_smiling"
      ],
      [
        "code" => "hand_fingers_splayed"
      ],
      [
        "code" => "hatching_chick"
      ],
      [
        "code" => "heart"
      ],
      [
        "code" => "hibiscus"
      ],
      [
        "code" => "high_voltage"
      ],
      [
        "code" => "hundred_points"
      ],
      [
        "code" => "jack-o-lantern"
      ],
      [
        "code" => "kite"
      ],
      [
        "code" => "lady_beetle"
      ],
      [
        "code" => "lion"
      ],
      [
        "code" => "lollipop"
      ],
      [
        "code" => "lotus"
      ],
      [
        "code" => "mage"
      ],
      [
        "code" => "maple_leaf"
      ],
      [
        "code" => "mobile_phone"
      ],
      [
        "code" => "music_guitare"
      ],
      [
        "code" => "national_park"
      ],
      [
        "code" => "nature_growing"
      ],
      [
        "code" => "nature_leaf"
      ],
      [
        "code" => "nature_tree"
      ],
      [
        "code" => "panda"
      ],
      [
        "code" => "parachute"
      ],
      [
        "code" => "parrot"
      ],
      [
        "code" => "party_beer"
      ],
      [
        "code" => "party_face"
      ],
      [
        "code" => "peacock"
      ],
      [
        "code" => "penguin"
      ],
      [
        "code" => "pinata"
      ],
      [
        "code" => "pineapple"
      ],
      [
        "code" => "ping_pong"
      ],
      [
        "code" => "popcorn"
      ],
      [
        "code" => "rainbow"
      ],
      [
        "code" => "red_apple"
      ],
      [
        "code" => "ringed_planet"
      ],
      [
        "code" => "robot"
      ],
      [
        "code" => "rocket"
      ],
      [
        "code" => "rose"
      ],
      [
        "code" => "rosette"
      ],
      [
        "code" => "shooting_star"
      ],
      [
        "code" => "smiling_face"
      ],
      [
        "code" => "smiling_face_halo"
      ],
      [
        "code" => "smiling_face_hearts"
      ],
      [
        "code" => "smiling_face_heart_eyes"
      ],
      [
        "code" => "snowflake"
      ],
      [
        "code" => "sparkles"
      ],
      [
        "code" => "sport_football"
      ],
      [
        "code" => "star"
      ],
      [
        "code" => "star_struck"
      ],
      [
        "code" => "sun"
      ],
      [
        "code" => "sunflower"
      ],
      [
        "code" => "t-rex"
      ],
      [
        "code" => "television"
      ],
      [
        "code" => "theatre"
      ],
      [
        "code" => "tiger_face"
      ],
      [
        "code" => "trophy"
      ],
      [
        "code" => "tropical_drink"
      ],
      [
        "code" => "tropical_fish"
      ],
      [
        "code" => "tulip"
      ],
      [
        "code" => "unicorn"
      ],
      [
        "code" => "victory_hand"
      ],
      [
        "code" => "video_games_joystick"
      ],
      [
        "code" => "volcano"
      ],
      [
        "code" => "wrapped_gift"
      ]
    ];

    $this->db->table("emoji")->insertBatch($data);
  }
}
