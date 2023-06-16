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

        return $this->isLastQuerySuccessful(); 
    }

    public function hideConversation(int $idUser, int $idFriend){
        $this->db->table("conversation")
        ->set("hidden", 1)
        ->groupStart()
            ->where("idUser1", $idUser)
            ->orWhere("idUser1", $idFriend)
        ->groupEnd()
        ->groupStart()
            ->where("idUser2", $idUser)
            ->orWhere("idUser2", $idFriend)
        ->groupEnd()
        ->update();
    }

    public function unhideConversation(int $idUser, int $idFriend){
        $this->db->table("conversation")
        ->set("hidden", 0)
        ->groupStart()
            ->where("idUser1", $idUser)
            ->orWhere("idUser1", $idFriend)
        ->groupEnd()
        ->groupStart()
            ->where("idUser2", $idUser)
            ->orWhere("idUser2", $idFriend)
        ->groupEnd()
        ->update();
    }

    public function conversationExists(int $idUser, int $idFriend){
        
        $array = [
            "idUser1" => $idUser,
            "idUser2" => $idFriend
        ];
    
        $array2 = [
            "idUser1" => $idFriend,
            "idUser2" => $idUser
        ];
    
        $data = $this->db
            ->table("conversation")
            ->groupStart()
            ->where($array)
            ->groupEnd()
            ->orGroupStart()
            ->where($array2)
            ->groupEnd()
            ->get()
            ->getRowArray();
    
            return $data != null;
    }

    public function getAllConversations(int $idUser) : array {
        return $this->db->table("conversation")
        ->groupStart()
        ->where("idUser1", $idUser)
        ->orWhere("idUser2", $idUser)
        ->groupEnd()
        ->where("hidden", 0)
        ->orderBy("sentAt", "DESC")
        ->get()
        ->getResultObject();       
    }

    public function updateLastMessage(int $idConversation, string $content) : bool {
        $now = new DateTime();
        $now = $now->format("Y-m-d H:i:s");

        $this->db->table("conversation")->update(["lastMessage" => $content, "sentAt" => $now], ["id"=>$idConversation]);

        return $this->isLastQuerySuccessful();
    }
}