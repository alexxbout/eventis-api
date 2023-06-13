<?php

namespace App\Models;

use CodeIgniter\Model;

use DateTime;

class MessageModel extends BaseModel
{

    //version A
    public function getConversation(int $id1, int $id2, int $idConversation): array {
        return $this->db->table("message")
            ->select("idSender, idReceiver, content, sentAt")
            ->where("idConversation", $idConversation)
            ->groupStart()
            ->where("idSender", $id1)
            ->orWhere("idReceiver", $id1)
            ->groupEnd()
            ->groupStart()
            ->where("idSender", $id2)
            ->orWhere("idReceiver", $id2)
            ->groupEnd()
            ->orderBy("sentAt", "DESC")
            ->limit(20)
            ->get()
            ->getResultObject();
    }

    //version B
    /*public function getConversation($id1, $id2, $idConversation, $multiple): array {
        $nbMsg = $multiple * 20;
        return $this->db->table("message")
            ->select("idSender, idReceiver, content, sentAt")
            ->where("idConversation", $idConversation)
            ->groupStart()
            ->where("idSender", $id1)
            ->orWhere("idReceiver", $id1)
            ->groupEnd()
            ->groupStart()
            ->where("idSender", $id2)
            ->orWhere("idReceiver", $id2)
            ->groupEnd()
            ->orderBy("sentAt", "DESC")
            ->limit($nbMsg)
            ->get()
            ->getResultObject();
    }*/

    public function markAsRead(int $idUser, int $idFriend, int $idConversation): bool {
        $this->db->table("message")
        ->where("idReceiver", $idUser)
        ->where("idSender", $idFriend)
        ->where("idConversation", $idConversation)
        ->update(["unread" => 0]);

        return $this->isLastQuerySuccessfull();
    }

    public function add(object $message): bool {
        $message->id = $this->getMax("message", "id") + 1;
        $message->sentAt = new DateTime();

        $this->db->table("message")->insert($message);

        return $this->isLastQuerySuccessfull();
    }

    public function countUnreadMessages(int $idUser, int $idFriend, int $idConversation): int {
        $builder = $this->db->table('message');
        $builder->selectCount('id');
        $builder->where('idReceiver', $idUser);
        $builder->where('idSender', $idFriend);
        $builder->where('idConversation', $idConversation);
        $builder->where('unread', 1);
    
        $result = $builder->get()->getRow();
    
        if ($result !== null) {
            return $result->id;
        } else {
            return 0;
        }
    }
}