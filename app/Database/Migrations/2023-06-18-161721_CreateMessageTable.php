<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateMessageTable extends Migration {
  public function up() {
    $this->db->disableForeignKeyChecks();

    $this->forge->addField([
      "id" => [
        "type" => "INT",
      ],
      "idConversation" => [
        "type" => "INT",
      ],
      "idSender" => [
        "type" => "INT",
      ],
      "idReceiver" => [
        "type" => "INT",
      ],
      "content" => [
        "type" => "VARCHAR",
        "constraint" => 255,
      ],
      "sentAt" => [
        "type" => "TIMESTAMP",
        "null" => false,
        "default" => new RawSql('CURRENT_TIMESTAMP'),
      ],
      "unread" => [
        "type" => "INT",
      ],
    ]);

    $this->forge->addKey("id", true);

    $this->forge->addForeignKey("idConversation", "conversation", "id", "RESTRICT", "RESTRICT");
    $this->forge->addForeignKey("idSender", "user", "id", "RESTRICT", "RESTRICT");
    $this->forge->addForeignKey("idReceiver", "user", "id", "RESTRICT", "RESTRICT");

    $this->forge->createTable("message");

    $this->db->enableForeignKeyChecks();
  }

  public function down() {
    $this->forge->dropTable("message");
  }
}
