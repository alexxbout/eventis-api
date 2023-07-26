<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class EmojiModelTest extends CIUnitTestCase {

  use DatabaseTestTrait;

  // For Migrations
  protected $migrate = true;
  protected $migrateOnce = false;
  protected $refresh = true;
  protected $namespace = null;

  // For Seeds
  protected $seed = \App\Database\Seeds\TestSeeder::class;
  
  public function testGetAll() {
    $model = new \App\Models\EmojiModel();
    $emojis = $model->getAll();

    $this->assertCount(106, $emojis);

    $expectedCodes = [
      "alien",
      "arts_brush",
      "arts_painting",
      "arts_palette",
      "balloon",
      "baseball",
      "basketball",
      "beach_umbrella",
      "bear",
      "beer_mug",
      "bird",
      "blossom",
      "board_games_card",
      "board_games_dice",
      "book_open",
      "bowling",
      "butterfly",
      "camping",
      "cat_face",
      "christmas_tree",
      "cinema_cam",
      "cinema_clap",
      "cinema_strip",
      "circus_tent",
      "cooking_sandwich",
      "crown",
      "cupcake",
      "desert",
      "desert_island",
      "disco_ball",
      "dog_face",
      "dolphin",
      "dragon_face",
      "droplet",
      "ferris_wheel",
      "fire",
      "flamingo",
      "flying_saucer",
      "four_leaf_clover",
      "fox",
      "ghost",
      "globe",
      "grinning_face_smiling",
      "hand_fingers_splayed",
      "hatching_chick",
      "heart",
      "hibiscus",
      "high_voltage",
      "hundred_points",
      "jack-o-lantern",
      "kite",
      "lady_beetle",
      "lion",
      "lollipop",
      "lotus",
      "mage",
      "maple_leaf",
      "mobile_phone",
      "music_guitare",
      "national_park",
      "nature_growing",
      "nature_leaf",
      "nature_tree",
      "panda",
      "parachute",
      "parrot",
      "party_beer",
      "party_face",
      "peacock",
      "penguin",
      "pinata",
      "pineapple",
      "ping_pong",
      "popcorn",
      "rainbow",
      "red_apple",
      "ringed_planet",
      "robot",
      "rocket",
      "rose",
      "rosette",
      "shooting_star",
      "smiling_face",
      "smiling_face_halo",
      "smiling_face_hearts",
      "smiling_face_heart_eyes",
      "snowflake",
      "sparkles",
      "sport_football",
      "star",
      "star_struck",
      "sun",
      "sunflower",
      "t-rex",
      "television",
      "theatre",
      "tiger_face",
      "trophy",
      "tropical_drink",
      "tropical_fish",
      "tulip",
      "unicorn",
      "victory_hand",
      "video_games_joystick",
      "volcano",
      "wrapped_gift"
    ];

    foreach ($emojis as $emoji) {
      $this->assertContains($emoji->code, $expectedCodes);
    }
  }
}
