<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TestSeeder extends Seeder {
  public function run() {
    // Call all seeders : 18 seeders
    $this->call("RoleSeeder");
    $this->call("EmojiSeeder");
    $this->call("EventCategorySeeder");
    $this->call("NotificationTypeSeeder");
    $this->call("InterestSeeder");
    $this->call("FoyerSeeder");
    $this->call("EventSeeder");
    $this->call("UserSeeder");
    $this->call("UserInterestSeeder");
    $this->call("BlockedSeeder");
    $this->call("CodeSeeder");
    $this->call("ConversationSeeder");
    $this->call("FriendRequestSeeder");
    $this->call("FriendSeeder");
    $this->call("MessageSeeder");
    $this->call("NotificationSeeder");
    $this->call("ParticipantSeeder");
    $this->call("RegistrationSeeder");
  }
}
