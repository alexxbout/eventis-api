<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * @internal
 */
final class RoleModelTest extends CIUnitTestCase {

  use DatabaseTestTrait;

  // For Migrations
  protected $migrate = true;
  protected $migrateOnce = false;
  protected $refresh = true;
  protected $namespace = null;

  // For Seeds
  protected $seed = \App\Database\Seeds\TestSeeder::class;

  public function testGetAll() {
    $model = new \App\Models\RoleModel();

    $objects = $model->getAll();

    $this->assertCount(4, $objects);
  }
}
