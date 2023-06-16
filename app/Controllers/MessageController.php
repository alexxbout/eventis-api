<?php

namespace App\Controllers;
use App\Models\MessageModel;
use App\Models\FriendModel;
use App\Models\ConversationModel;
use App\Utils\HTTPCodes;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use DateTime;

class MessageController extends BaseController{

    private const NOT_YOU           = "Impossible de regarder des messages d'un compte qui n'est pas le votre";
    private const NO_CONTENT        = "Conversation vide";
    private const CONVERSATION_GET  = "Vingt derniers messages issus de la conversation";
    private const NOT_YOUR_FRIEND   = "Vous ne pouvez envoyer des messages qu'à vos amis";
    private const MESSAGE_SENT      = "Votre message a été envoyé";
    private const MARKED_AS_READ    = "Messages lus";
    private const SERVER_PROBLEM    = "Quelque chose d'inattendu s'est passé";

    private MessageModel $messageModel;
    private FriendModel $friendModel;
    private ConversationModel $conversationModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->messageModel = new \App\Models\MessageModel();
        $this->friendModel = new \App\Models\FriendModel();
        $this->conversationModel = new \App\Models\ConversationModel();
    }

    public function getOldMessages(int $idUser, int $idFriend, int $idConversation, int $date, int $offset){
        if($this->user->getId() == $idUser){
            $date = date("Y-m-d", $date / 1000);
            $data = $this->messageModel->getOldMessages($idUser, $idFriend, $idConversation, $date, $offset);
            if(empty($data)){
                $this->send(HTTPCodes::NO_CONTENT, $data, self::NO_CONTENT);
            } else {
                $this->send(HTTPCodes::OK, $data, self::CONVERSATION_GET);
            }
        } else {
            $this->send(HTTPCodes::FORBIDDEN, null, self::NOT_YOU);
        }
    }

    public function getNewMessages(int $idUser, int $idFriend, int $idConversation, int $date){
        if($this->user->getId() == $idUser){
            $date = date("Y-m-d", $date / 1000);
            log_message('debug', $date);
            $data = $this->messageModel->getNewMessages($idUser, $idFriend, $idConversation, $date);
            if(empty($data)){
                $this->send(HTTPCodes::NO_CONTENT, $data, self::NO_CONTENT);
            } else {
                $this->send(HTTPCodes::OK, $data, self::CONVERSATION_GET);
            }
        } else {
            $this->send(HTTPCodes::FORBIDDEN, null, self::NOT_YOU);
        }
    }

    /*
    public function getConversation(int $idUser, int $idFriend, int $idConversation, int $date, int $offset){
        if($this->user->getId() == $idUser){
            $date = date("Y-m-d", $date / 1000);
            $data = $this->messageModel->getOldMessages($idUser, $idFriend, $idConversation, $date, $offset);
            if(empty($data)){
                $this->send(HTTPCodes::NO_CONTENT, $data, self::NO_CONTENT);
            } else {
                $this->send(HTTPCodes::OK, $data, self::CONVERSATION_GET);
            }
        } else {
            $this->send(HTTPCodes::FORBIDDEN, null, self::NOT_YOU);
        }
    }*/

    public function markAsRead(int $idUser, int $idFriend, int $idConversation){
        if($this->user->getId() == $idUser){
            $success = $this->messageModel->markAsRead($idUser, $idFriend, $idConversation);
            if($success){
                $this->send(HTTPCodes::OK, null, self::MARKED_AS_READ);
            } else {
                $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, null, self::SERVER_PROBLEM);
            }
        } else {
            $this->send(HTTPCodes::FORBIDDEN, null, self::NOT_YOU);
        }
    }

    public function sendMessage(int $idUser, int $idFriend, int $idConversation){
        if($this->user->getId() == $idUser && $this->friendModel->isFriend($idUser, $idFriend)){
            $message = $this->request->getJSON();
            $message->idSender = $idUser;
            $message->idReceiver = $idFriend;
            $message->idConversation = $idConversation;
            $message->unread = 1;
            

            $check1 = $this->messageModel->add($message);
            $check2 = $this->conversationModel->updateLastMessage($idConversation, $message->content);
            if($check1 && $check2){
                $this->send(HTTPCodes::CREATED, $message, self::MESSAGE_SENT);
            } else {
                $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, null, self::SERVER_PROBLEM);
            }
            
        } else {
            $this->send(HTTPCodes::FORBIDDEN, null, self::NOT_YOUR_FRIEND);
        }
    }

    //pas sur si necessaire ou si bien formatter
    public function nbDeUnread(int $idUser, int $idFriend, int $idConversation){
        if($this->user->getId() == $idUser){
            return $this->messageModel->unreadCheck($idUser, $idFriend, $idConversation);
        } else {
            $this->send(HTTPCodes::FORBIDDEN, null, self::NOT_YOUR_FRIEND);
        }
    }
}