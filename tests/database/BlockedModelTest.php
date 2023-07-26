<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

final class BlockedModelTest extends CIUnitTestCase {

  use DatabaseTestTrait;

  // For Migrations
  protected $migrate = true;
  protected $migrateOnce = false;
  protected $refresh = true;
  protected $namespace = null;

  // For Seeds
  protected $seed = \App\Database\Seeds\TestSeeder::class;

  public function testGetAll() {
    $model = new \App\Models\BlockedModel();

    $idUser = 1;
    $blocked = $model->getAll($idUser);

    $this->assertCount(1, $blocked);
    $this->assertEquals(0, $blocked[0]->id);
    $this->assertEquals(1, $blocked[0]->idUser);
    $this->assertEquals(5, $blocked[0]->idBlocked);
  }

  public function testIsBlocked() {
    $model = new \App\Models\BlockedModel();

    $idUser = 1;
    $idBlocked = 5;

    $isBlocked = $model->isBlocked($idUser, $idBlocked);

    $this->assertTrue($isBlocked);
  }

  public function testAdd() {
    $model = new \App\Models\BlockedModel();

    $idUser = 1;
    $idBlocked = 2;

    $insertedId = $model->add($idUser, $idBlocked);

    $this->assertNotEquals(-1, $insertedId);

    $isBlocked = $model->isBlocked($idUser, $idBlocked);

    $this->assertTrue($isBlocked);
  }

  public function testRemove() {
    $model = new \App\Models\BlockedModel();

    $idUser = 1;
    $idBlocked = 5;

    $isBlockedBefore = $model->isBlocked($idUser, $idBlocked);

    $this->assertTrue($isBlockedBefore);

    $removed = $model->remove($idUser, $idBlocked);

    $this->assertTrue($removed);

    $isBlockedAfter = $model->isBlocked($idUser, $idBlocked);

    $this->assertFalse($isBlockedAfter);
  }
}
