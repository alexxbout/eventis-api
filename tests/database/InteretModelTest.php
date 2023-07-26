<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * @internal
 */
final class InteretModelTest extends CIUnitTestCase {

  use DatabaseTestTrait;

  // For Migrations
  protected $migrate = true;
  protected $migrateOnce = false;
  protected $refresh = true;
  protected $namespace = null;

  // For Seeds
  protected $seed = \App\Database\Seeds\TestSeeder::class;
  
  public function testGetInterestByUser() {
    $model = new \App\Models\InteretModel();
    $interests = $model->getInterestByUser(1);

    $this->assertCount(3, $interests);

    $expectedInterests = [
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
      ]
    ];

    foreach ($interests as $index => $interest) {
      $this->assertEquals($expectedInterests[$index]["id"], $interest->id);
      $this->assertEquals($expectedInterests[$index]["name"], $interest->name);
      $this->assertEquals($expectedInterests[$index]["emoji"], $interest->emoji);
      $this->assertEquals($expectedInterests[$index]["color"], $interest->color);
    }
  }

  public function testHasInterest() {
    $model = new \App\Models\InteretModel();

    $this->assertTrue($model->hasInterest(1, 1));
    $this->assertFalse($model->hasInterest(1, 4));
  }

  public function testAdd() {
    $model = new \App\Models\InteretModel();

    $this->assertTrue($model->add(1, 4));
    $this->assertTrue($model->hasInterest(1, 4));
  }

  public function testRemove() {
    $model = new \App\Models\InteretModel();

    $this->assertTrue($model->remove(1, 1));
    $this->assertFalse($model->hasInterest(1, 1));
  }

  public function testGetAll() {
    $model = new \App\Models\InteretModel();
    $interests = $model->getAll();

    $this->assertCount(10, $interests);

    $expectedInterests = [
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

    foreach ($interests as $index => $interest) {
      $this->assertEquals($expectedInterests[$index]["id"], $interest->id);
      $this->assertEquals($expectedInterests[$index]["name"], $interest->name);
      $this->assertEquals($expectedInterests[$index]["emoji"], $interest->emoji);
      $this->assertEquals($expectedInterests[$index]["color"], $interest->color);
    }
  }
}
