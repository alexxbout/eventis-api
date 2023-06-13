<?php

namespace App\Models;

use CodeIgniter\Model;

use DateTime;

class ConversationModel extends BaseModel
{
    public function createConversation(int $idUser, int $idFriend){
        $data = [
            'id' => $this->getMax("conversation", "id") + 1,
            'idUser1' => $idUser,
            'idUser2' => $idFriend
        ];

        $this->db->table("conversation")->insert($data);

        return $this->isLastQuerySuccessfull(); 
    }

    public function getAllConversations(int $idUser) : array {
        return $this->db->table("conversation")
            ->groupStart()
            ->where("idUser1", $idUser)
            ->orWhere("idUser2", $idUser)
            ->groupEnd()
            ->where("hidden !=", 1)
            ->orderBy("sentAt", "DESC")
            ->get()
            ->getResultObject();
    }

    public function updateLastMessage(int $idConversation, string $content) : bool {
        $now = new DateTime();
        $now = $now->format("Y-m-d H:i:s");
        log_message("debug", $content);

        $this->db->table("conversation")->update(["lastMessage" => $content, "sentAt" => $now], ["id"=>$idConversation]);

        log_message('debug',$this->db->getLastQuery());
        log_message('debug', $this->isLastQuerySuccessful());
        return $this->isLastQuerySuccessful();
    }
}