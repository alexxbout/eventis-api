<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class FoyerModelTest extends CIUnitTestCase {

  use DatabaseTestTrait;

  // For Migrations
  protected $migrate = true;
  protected $migrateOnce = false;
  protected $refresh = true;
  protected $namespace = null;

  // For Seeds
  protected $seed = \App\Database\Seeds\TestSeeder::class;

  public function testGetAll() {
    $model = new \App\Models\FoyerModel();
    $foyers = $model->getAll();

    $this->assertCount(3, $foyers);

    $expectedFoyers = [
      [
        "id" => 1,
        "city" => "Rennes",
        "zip" => "35000",
        "address" => "1 rue de la paix"
      ],
      [
        "id" => 2,
        "city" => "La Gacilly",
        "zip" => "56200",
        "address" => "2 rue de la paix"
      ],
      [
        "id" => 3,
        "city" => "Nantes",
        "zip" => "44000",
        "address" => "3 rue de la paix"
      ]
    ];

    foreach ($foyers as $index => $foyer) {
      $this->assertEquals($expectedFoyers[$index]["id"], $foyer->id);
      $this->assertEquals($expectedFoyers[$index]["city"], $foyer->city);
      $this->assertEquals($expectedFoyers[$index]["zip"], $foyer->zip);
      $this->assertEquals($expectedFoyers[$index]["address"], $foyer->address);
    }
  }

  public function testGetById() {
    $model = new \App\Models\FoyerModel();
    $foyer = $model->getById(1);

    $this->assertNotNull($foyer);
    $this->assertEquals(1, $foyer->id);
    $this->assertEquals("Rennes", $foyer->city);
    $this->assertEquals("35000", $foyer->zip);
    $this->assertEquals("1 rue de la paix", $foyer->address);
  }

  public function testGetByZip() {
    $model = new \App\Models\FoyerModel();
    $foyers = $model->getByZip(35000);

    $this->assertCount(1, $foyers);

    $expectedFoyer = [
      "id" => 1,
      "city" => "Rennes",
      "zip" => "35000",
      "address" => "1 rue de la paix"
    ];

    $foyer = $foyers[0];

    $this->assertEquals($expectedFoyer["id"], $foyer->id);
    $this->assertEquals($expectedFoyer["city"], $foyer->city);
    $this->assertEquals($expectedFoyer["zip"], $foyer->zip);
    $this->assertEquals($expectedFoyer["address"], $foyer->address);
  }

  public function testAdd() {
    $model = new \App\Models\FoyerModel();
    $newFoyerData = (object)[
      "id" => 4,
      "city" => "Paris",
      "zip" => "75000",
      "address" => "4 rue de la paix"
    ];

    $newFoyerId = $model->add($newFoyerData);

    $this->assertEquals(4, $newFoyerId);

    // Retrieve the added foyer and check if the data matches
    $addedFoyer = $model->getById(4);

    $this->assertNotNull($addedFoyer);
    $this->assertEquals($newFoyerData->id, $addedFoyer->id);
    $this->assertEquals($newFoyerData->city, $addedFoyer->city);
    $this->assertEquals($newFoyerData->zip, $addedFoyer->zip);
    $this->assertEquals($newFoyerData->address, $addedFoyer->address);
  }
}
