<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class EventModelTest extends CIUnitTestCase {

  use DatabaseTestTrait;

  // For Migrations
  protected $migrate = true;
  protected $migrateOnce = false;
  protected $refresh = true;
  protected $namespace = null;

  // For Seeds
  protected $seed = \App\Database\Seeds\TestSeeder::class;

  public function testGetAll() {
    $eventModel = new \App\Models\EventModel();

    $events = $eventModel->getAll();

    $expectedEvents = [
      (object)[
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
      (object)[
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
      (object)[
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
      (object)[
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
      (object)[
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

    $this->assertEquals($expectedEvents, $events);
  }

  public function testGetAllNC() {
    $model = new \App\Models\EventModel();

    $result = $model->getAllNC();

    $this->assertIsArray($result);
    $this->assertCount(4, $result);
  }

  public function testGetIdFoyerByIdEvent() {
    $model = new \App\Models\EventModel();

    $id = 1;
    $result = $model->getIdFoyerByIdEvent($id);

    $this->assertEquals(1, $result);
  }

  public function testGetByIdNC() {
    $model = new \App\Models\EventModel();

    $id = 2;
    $result = $model->getByIdNC($id);

    $this->assertNotNull($result);
    $this->assertEquals(2, $result->id);
    $this->assertEquals(1, $result->idFoyer);
    $this->assertEquals("35", $result->department);
    $this->assertEquals("1 rue de la paix", $result->address);
    $this->assertEquals("Rennes", $result->city);
    $this->assertEquals(0, $result->canceled);
    $this->assertNull($result->reason);
    $this->assertEquals(date("Y-m-d", strtotime("+1 months")), $result->start);
    $this->assertEquals("Sortie cinéma", $result->title);
    $this->assertEquals(4, $result->idCategory);
    $this->assertEquals("Venez voir le dernier film de Marvel !", $result->description);
    $this->assertNull($result->pic);
  }

  public function testGetByIdCanceled() {
    $model = new \App\Models\EventModel();

    $result = $model->getByIdCanceled(5);

    $this->assertNotNull($result);
    $this->assertEquals("Soirée jeux vidéo", $result->title);
  }

  public function testGetById() {
    $model = new \App\Models\EventModel();

    $id = 1;
    $result = $model->getById($id);

    $this->assertNotNull($result);
    $this->assertEquals("35", $result->department);
    $this->assertEquals("1 rue de la paix", $result->address);
    $this->assertEquals("Rennes", $result->city);
    $this->assertEquals(0, $result->canceled);
    $this->assertNull($result->reason);
    $this->assertEquals(date("Y-m-d", strtotime("+1 months")), $result->start);
    $this->assertEquals("Fête de la musique", $result->title);
    $this->assertEquals(6, $result->idCategory);
    $this->assertEquals("Venez à cette fête de la musique, on va s'éclater !", $result->description);
    $this->assertEquals("this/is/a/test/path", $result->pic);
  }

  public function testGetByDepartment() {
    $model = new \App\Models\EventModel();

    $department = "35";
    $result = $model->getByDepartment($department);

    $this->assertIsArray($result);
    $this->assertCount(3, $result);
    $this->assertEquals("35", $result[0]->department);
    $this->assertEquals("1 rue de la paix", $result[0]->address);
    $this->assertEquals("Rennes", $result[0]->city);
    $this->assertEquals(0, $result[0]->canceled);
    $this->assertNull($result[0]->reason);
    $this->assertEquals(date("Y-m-d", strtotime("+1 months")), $result[0]->start);
    $this->assertEquals("Fête de la musique", $result[0]->title);
    $this->assertEquals(6, $result[0]->idCategory);
    $this->assertEquals("Venez à cette fête de la musique, on va s'éclater !", $result[0]->description);
    $this->assertEquals("this/is/a/test/path", $result[0]->pic);
  }

  public function testCancel() {
    $model = new \App\Models\EventModel();

    $id = 1;
    $reason = "Mauvais temps";
    $model->cancel($id, $reason);

    $result = $model->getById($id);

    $this->assertNotNull($result);
    $this->assertEquals(1, $result->canceled);
    $this->assertEquals($reason, $result->reason);
  }

  public function testUncancel() {
    $model = new \App\Models\EventModel();

    $id = 1;
    $model->uncancel($id);

    $result = $model->getById($id);

    $this->assertNotNull($result);
    $this->assertEquals(0, $result->canceled);
    $this->assertNull($result->reason);
  }

  public function testUpdateData() {
    $model = new \App\Models\EventModel();

    $id = 1;
    $data = new stdClass();
    $data->title = "Fête de la musique 2";
    $data->description = "Venez à cette fête de la musique, on va s'éclater encore plus !";

    $model->updateData($id, $data);

    $result = $model->getById($id);

    $this->assertNotNull($result);
    $this->assertEquals("Fête de la musique 2", $result->title);
    $this->assertEquals("Venez à cette fête de la musique, on va s'éclater encore plus !", $result->description);
  }

  public function testAdd() {
    $model = new \App\Models\EventModel();

    $data = new stdClass();
    $data->idFoyer = 1;
    $data->department = "35";
    $data->address = "1 rue de la paix";
    $data->city = "Rennes";
    $data->canceled = 0;
    $data->reason = null;
    $data->start = date("Y-m-d", strtotime("+2 months"));
    $data->title = "Soirée karaoké";
    $data->idCategory = 3;
    $data->description = "Venez chanter avec nous !";
    $data->pic = null;

    $model->add($data);

    $result = $model->getById(6);

    $this->assertNotNull($result);
    $this->assertEquals(1, $result->idFoyer);
    $this->assertEquals("35", $result->department);
    $this->assertEquals("1 rue de la paix", $result->address);
    $this->assertEquals("Rennes", $result->city);
    $this->assertEquals(0, $result->canceled);
    $this->assertNull($result->reason);
    $this->assertEquals(date("Y-m-d", strtotime("+2 months")), $result->start);
    $this->assertEquals("Soirée karaoké", $result->title);
    $this->assertEquals(3, $result->idCategory);
    $this->assertEquals("Venez chanter avec nous !", $result->description);
    $this->assertNull($result->pic);
  }

  public function testAddImage() {
    $model = new \App\Models\EventModel();

    $id = 1;
    $path = "this/is/a/test/path";
    $model->addImage($id, $path);

    $result = $model->getById($id);

    $this->assertNotNull($result);
    $this->assertEquals($path, $result->pic);
  }

  public function testGetImage() {
    $model = new \App\Models\EventModel();

    $id = 1;
    $result = $model->getImage($id);

    $this->assertNotNull($result);
    $this->assertEquals("this/is/a/test/path", $result);
  }

  public function testGetEventForTime() {
    $model = new \App\Models\EventModel();

    $result = $model->getDistinctDates(2);

    $this->assertIsArray($result);
    $this->assertCount(2, $result);

    $this->assertEquals(date("Y-m-d", strtotime("+1 months")), $result[0]->start);
    $this->assertEquals(date("Y-m-d", strtotime("+2 months")), $result[1]->start);
  }

  public function testGetByDayAndDepartment() {
    $model = new \App\Models\EventModel();

    $day = date("Y-m-d", strtotime("+1 months"));
    $department = "35";
    $result = $model->getByDayAndDepartment($day, $department);

    $this->assertIsArray($result);
    $this->assertCount(2, $result);
    $this->assertEquals("Fête de la musique", $result[0]->title);
    $this->assertEquals("Sortie cinéma", $result[1]->title);
  }

  public function testGetByDepartmentNC() {
    $model = new \App\Models\EventModel();

    $department = "35";
    $result = $model->getByDepartmentNC($department);

    $this->assertIsArray($result);
    $this->assertCount(3, $result);
  }
}
