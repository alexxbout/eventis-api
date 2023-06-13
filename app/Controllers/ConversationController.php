<?php

namespace App\Controllers;
use App\Models\MessageModel;
use App\Models\FriendModel;
use App\Models\ConversationModel;
use App\Utils\HTTPCodes;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use PDO;
use Psr\Log\LoggerInterface;

class ConversationController extends BaseController{ 
    private const NOT_YOU               = "Impossible de regarder des messages d'un compte qui n'est pas le votre";
    private const ALL_CONVERSATIONS_GET = "Toutes les conversations";
    private const MESSAGES_PAGE_EMPTY   = "Vous n'avez pas de conversation!";
    private const CONVERSATION_HIDDEN   = "Conversation supprimée";

    private FriendModel $friendModel;
    private ConversationModel $conversationModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->friendModel = new \App\Models\FriendModel();
        $this->conversationModel = new \App\Models\ConversationModel();
    }

    public function getAllConversations($idUser){
        if($this->user->getId() == $idUser){
            $data = $this->conversationModel->getAllConversations($idUser);
            if(empty($data)){
                $this->send(HTTPCodes::NO_CONTENT, $data, self::MESSAGES_PAGE_EMPTY);
            } else {
                $this->send(HTTPCodes::OK, $data, self::ALL_CONVERSATIONS_GET);
            }
           
        } else {
            $this->send(HTTPCodes::FORBIDDEN, null, self::NOT_YOU);
        }
    }

    /* //c'est quoi hide? pour blocked?
    public function hideConversation($idUser, $idConversation){
        if($this->user->getId() == $idUser){
            $this->conversationModel->hideConversation($idConversation);
            $this->send(HTTPCodes::OK, null, self::CONVERSATION_HIDDEN);
        } else {
            $this->send(HTTPCodes::FORBIDDEN, null, self::NOT_YOU);
        }

    }*/
}