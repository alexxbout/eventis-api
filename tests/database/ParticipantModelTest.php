<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * @internal
 */
final class ParticipantModelTest extends CIUnitTestCase {

  use DatabaseTestTrait;

  // For Migrations
  protected $migrate = true;
  protected $migrateOnce = false;
  protected $refresh = true;
  protected $namespace = null;

  // For Seeds
  protected $seed = \App\Database\Seeds\TestSeeder::class;

  public function testGetAll() {
    $model = new \App\Models\ParticipantModel();
  
    $result = $model->getAll(1);
  
    $this->assertCount(5, $result);
  }
  
  public function testAdd() {
    $model = new \App\Models\ParticipantModel();
  
    $id = $model->add(5, 1);
  
    $this->assertGreaterThan(0, $id);
  }
  
  public function testRemove() {
    $model = new \App\Models\ParticipantModel();
  
    $result = $model->remove(1, 1);
  
    $this->assertTrue($result);
  }
  
  public function testIsParticipating() {
    $model = new \App\Models\ParticipantModel();
  
    $result = $model->isParticipating(1, 1);
  
    $this->assertTrue($result);
  }
}
