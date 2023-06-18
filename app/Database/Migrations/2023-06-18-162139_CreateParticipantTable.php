<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateParticipantTable extends Migration {
  public function up() {
    $this->db->disableForeignKeyChecks();

    $this->forge->addField([
      "id" => [
        "type" => "INT",
      ],
      "idEvent" => [
        "type" => "INT",
      ],
      "idUser" => [
        "type" => "INT",
      ],
    ]);

    $this->forge->addKey("id", true);

    $this->forge->addForeignKey("idEvent", "event", "id", "RESTRICT", "RESTRICT");
    $this->forge->addForeignKey("idUser", "user", "id", "RESTRICT", "RESTRICT");

    $this->forge->createTable("participant");

    $this->db->enableForeignKeyChecks();
  }

  public function down() {
    $this->forge->dropTable("participant");
  }
}
