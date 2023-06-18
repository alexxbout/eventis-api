<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateRegistrationTable extends Migration {
  public function up() {
    $this->db->disableForeignKeyChecks();

    $this->forge->addField([
      "id" => [
        "type" => "INT",
      ],
      "idCode" => [
        "type" => "INT",
      ],
      "idUser" => [
        "type" => "INT",
      ],
      "at" => [
        "type" => "TIMESTAMP",
        "default" => new RawSql("CURRENT_TIMESTAMP"),
        "null" => false,
      ],
    ]);

    $this->forge->addKey("id", true);

    $this->forge->addForeignKey("idCode", "code", "id", "RESTRICT", "RESTRICT");
    $this->forge->addForeignKey("idUser", "user", "id", "RESTRICT", "RESTRICT");

    $this->forge->createTable("registration");

    $this->db->enableForeignKeyChecks();
  }

  public function down() {
    $this->forge->dropTable("registration");
  }
}
