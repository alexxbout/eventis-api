<?php
namespace App\Controllers;

use App\Utils\HTTPCodes;
use App\Models\FriendModel;
use App\Models\UserModel;
use App\Models\FriendRequestModel;
use App\Models\BlockedModel;
use App\Models\NotificationModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class NotificationController extends BaseController {
    
    private const INVALID_ROLE      = "Rôle invalide";
    private const REMOVE_SUCCESS    = "Notification supprimée";
    private const USER_NOT_FOUND    = "Utilisateur introuvable";
    private const NOTIF_NOT_FOUND   = "Notification introuvable";
    private const NO_CONTENT        = "Rien n'a été trouvé";
    private const ALL_NOTIFS        = "Liste de notifications";
    private const EVENT_NOTIFS      = "Liste de notifications d'evenements";
    private const FRIEND_REQ_NOTIFS = "Liste de notifications de requetes d'amis";

    private NotificationModel $notificationModel;
    private FriendModel $friendModel;
    private UserModel $userModel;
    
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        parent::initController($request, $response, $logger);
        $this->friendModel = new FriendModel();
        $this->userModel = new userModel();
    }

    public function getAll($idUser) {
        if ($this->user->isDeveloper()) {
            if ($this->userModel->getById($idUser) == null) {
                $this->send(HTTPCodes::NOT_FOUND, null, self::USER_NOT_FOUND);
            } else {
                $data = $this->notificationModel->getAll($idUser);
                if(empty($data)){
                    $this->send(HTTPCodes::NO_CONTENT, $data, self::NO_CONTENT);
                } else {
                    $this->send(HTTPCodes::OK, $data, self::ALL_NOTIFS);
                }
            }
        } else {
            $this->send(HTTPCodes::FORBIDDEN, null, self::INVALID_ROLE);
        }
    }

    public function getNotifications($idUser) {
        if ($this->user->isDeveloper() || $this->user->getId() == $idUser) {
            $data1 = $this->notificationModel->getFriendRequestNotifications($idUser);
            $data2 = $this->notificationModel->getEventNotifications($idUser);
            $data = array_merge($data1, $data2);
            if(empty($data)){
                $this->send(HTTPCodes::NO_CONTENT, $data, self::NO_CONTENT);
            } else {
                $this->send(HTTPCodes::OK, $data, self::ALL_NOTIFS);
            }
        } else {
            $this->send(HTTPCodes::FORBIDDEN, null, self::INVALID_ROLE);
        }
    }

    public function getEventNotifications($idUser) {
        if ($this->user->isDeveloper() || $this->user->getId() == $idUser) {
            if ($this->userModel->getById($idUser) == null) {
                $this->send(HTTPCodes::NOT_FOUND, null, self::USER_NOT_FOUND);
            } else {
                $data = $this->notificationModel->getEventNotifications($idUser);
                if(empty($data)){
                    $this->send(HTTPCodes::NO_CONTENT, $data, self::NO_CONTENT);
                } else {
                    $this->send(HTTPCodes::OK, $data, self::EVENT_NOTIFS);
                }
            }
        } else {
            $this->send(HTTPCodes::FORBIDDEN, null, self::INVALID_ROLE);
        }
    }

    public function getFriendRequestNotifications($idUser) {
        if ($this->user->isDeveloper() || $this->user->getId() == $idUser) {
            if ($this->userModel->getById($idUser) == null) {
                $this->send(HTTPCodes::NOT_FOUND, null, self::USER_NOT_FOUND);
            } else {
                $data = $this->notificationModel->getFriendRequestNotifications($idUser);
                if(empty($data)){
                    $this->send(HTTPCodes::NO_CONTENT, $data, self::NO_CONTENT);
                } else {
                    $this->send(HTTPCodes::OK, $data, self::FRIEND_REQ_NOTIFS);
                }
            }
        } else {
            $this->send(HTTPCodes::FORBIDDEN, null, self::INVALID_ROLE);
        }
    }

    public function removeNotification($id) {
        if ($this->user->isDeveloper()){
            $result = $this->notificationModel->removeNotification($id);
            if($result){
                $this->send(HTTPCodes::OK, null, self::REMOVE_SUCCESS);
            } else {
                $this->send(HTTPCodes::NOT_FOUND, null, self::NOTIF_NOT_FOUND);
            }
        } else {
            $this->send(HTTPCodes::FORBIDDEN, null, self::INVALID_ROLE);
        }
    }
}