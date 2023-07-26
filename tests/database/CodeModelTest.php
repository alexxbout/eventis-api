<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

final class CodeModelTest extends CIUnitTestCase {

    use DatabaseTestTrait;
  
    // For Migrations
    protected $migrate = true;
    protected $migrateOnce = false;
    protected $refresh = true;
    protected $namespace = null;
  
    // For Seeds
    protected $seed = \App\Database\Seeds\TestSeeder::class;

    public function testGetAll() {
        $model = new \App\Models\CodeModel();

        $codes = $model->getAll();

        $this->assertCount(8, $codes);
        $this->assertEquals("AAAAA", $codes[0]->code);
        $this->assertEquals("AAAAB", $codes[1]->code);
        $this->assertEquals("AAAAC", $codes[2]->code);
        $this->assertEquals("AAAAD", $codes[3]->code);
        $this->assertEquals("AAAAE", $codes[4]->code);
        $this->assertEquals("AAAFA", $codes[5]->code);
        $this->assertEquals("AAAFB", $codes[6]->code);
        $this->assertEquals("AAAFD", $codes[7]->code);
    }

    public function testGetAllByFoyer() {
        $model = new \App\Models\CodeModel();

        $idFoyer = 1;
        $codes = $model->getAllByFoyer($idFoyer);

        $this->assertCount(8, $codes);
        $this->assertEquals("AAAAA", $codes[0]->code);
        $this->assertEquals("AAAAB", $codes[1]->code);
        $this->assertEquals("AAAAC", $codes[2]->code);
        $this->assertEquals("AAAAD", $codes[3]->code);
        $this->assertEquals("AAAAE", $codes[4]->code);
        $this->assertEquals("AAAFA", $codes[5]->code);
        $this->assertEquals("AAAFB", $codes[6]->code);
        $this->assertEquals("AAAFD", $codes[7]->code);
    }

    public function testGetByCode() {
        $model = new \App\Models\CodeModel();

        $code = "AAAAA";
        $result = $model->getByCode($code);

        $this->assertNotNull($result);
        $this->assertEquals("AAAAA", $result->code);
        $this->assertEquals(1, $result->idFoyer);
        $this->assertEquals(2, $result->createdBy);
        $this->assertEquals(2, $result->idRole);
    }

    public function testCheckExists() {
        $model = new \App\Models\CodeModel();

        $code = "AAAAA";
        $result = $model->checkExists($code);

        $this->assertTrue($result);

        $code = "ZZZZZ";
        $result = $model->checkExists($code);

        $this->assertFalse($result);
    }

    public function testSetUsed() {
        $model = new \App\Models\CodeModel();

        $id = 1;
        $result = $model->setUsed($id);

        $this->assertTrue(true);

        $code = "AAAAA";
        $result = $model->getByCode($code);

        $this->assertEquals(1, $result->used);
    }

    public function testAdd() {
        $model = new \App\Models\CodeModel();

        $code = "ZZZZZ";
        $idFoyer = 2;
        $expire = date("Y-m-d H:i:s", strtotime("+7 days"));
        $createdBy = 3;
        $idRole = 3;

        $result = $model->add($code, $idFoyer, $createdBy, $idRole, $expire);

        $this->assertGreaterThan(0, $result);

        $code = "ZZZZZ";
        $result = $model->getByCode($code);

        $this->assertNotNull($result);
        $this->assertEquals($idFoyer, $result->idFoyer);
        $this->assertEquals($expire, $result->expire);
        $this->assertEquals($createdBy, $result->createdBy);
        $this->assertEquals($idRole, $result->idRole);
    }

    public function testIsValid() {
        $model = new \App\Models\CodeModel();

        $idCode = 1;
        $result = $model->isValid($idCode);

        $this->assertFalse($result);

        $idCode = 6;
        $result = $model->isValid($idCode);

        $this->assertTrue($result);
    }

    public function testGetById() {
        $model = new \App\Models\CodeModel();

        $id = 1;
        $result = $model->getById($id);

        $this->assertNotNull($result);
        $this->assertEquals("AAAAA", $result->code);
        $this->assertEquals(1, $result->idFoyer);
        $this->assertEquals(2, $result->createdBy);
        $this->assertEquals(2, $result->idRole);
    }
}
