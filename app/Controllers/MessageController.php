<?php

namespace App\Controllers;
use App\Models\MessageModel;
use App\Models\FriendModel;
use App\Models\ConversationModel;
use App\Utils\HTTPCodes;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

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

    public function getConversation($idUser, $idFriend, $idConversation){
        if($this->user->getId() == $idUser){
            $data = $this->messageModel->getConversation($idUser, $idFriend, $idConversation);
            if(empty($data)){
                $this->send(HTTPCodes::NO_CONTENT, $data, self::NO_CONTENT);
            } else {
                $this->send(HTTPCodes::OK, $data, self::CONVERSATION_GET);
            }
        } else {
            $this->send(HTTPCodes::FORBIDDEN, null, self::NOT_YOU);
        }
    }

    //j'ai mis ca pour que on puisse l'utiliser SOIT dans la fonction au dessus SOIT a chaque fois que l'appli veut (ce qui est meilleur)
    public function markAsRead($idUser, $idFriend, $idConversation){
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

    public function sendMessage($idUser, $idFriend, $idConversation){
        if($this->user->getId() == $idUser && $this->friendModel->isFriend($idUser, $idFriend)){
            $message = $this->request->getJSON(); //message->content = the content of the message???
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
    public function nbDeUnread($idUser, $idFriend, $idConversation){
        if($this->user->getId() == $idUser){
            return $this->messageModel->unreadCheck($idUser, $idFriend, $idConversation);
        } else {
            $this->send(HTTPCodes::FORBIDDEN, null, self::NOT_YOUR_FRIEND);
        }
    }
}