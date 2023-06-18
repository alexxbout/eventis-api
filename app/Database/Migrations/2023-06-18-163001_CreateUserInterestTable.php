<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserInterestTable extends Migration {
  public function up() {
    $this->db->disableForeignKeyChecks();

    $this->forge->addField([
      "idUser" => [
        "type" => "INT",
      ],
      "idInterest" => [
        "type" => "INT",
      ],
    ]);

    $this->forge->addKey(["idUser", "idInterest"], true);

    $this->forge->addForeignKey("idUser", "user", "id", "RESTRICT", "RESTRICT");
    $this->forge->addForeignKey("idInterest", "interest", "id", "RESTRICT", "RESTRICT");

    $this->forge->createTable("user_interest");

    $this->db->enableForeignKeyChecks();
  }

  public function down() {
    $this->forge->dropTable("user_interest");
  }
}
