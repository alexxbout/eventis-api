<?php

namespace App\Controllers;

use App\Models\EventModel;
use App\Models\ParticipantModel;
use App\Models\UserModel;
use App\Utils\HTTPCodes;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class ParticipantController extends BaseController {
    private ParticipantModel $participantModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        parent::initController($request, $response, $logger);

        $this->participantModel = new ParticipantModel();
    }

    public function getAll(int $idEvent) {
        $this->send(HTTPCodes::OK, $this->participantModel->getAll($idEvent), "List of all participants of an event");
    }

    public function add(int $idEvent, int $idUser) {
        if ($this->user->isAdmin() || $this->user->isDeveloper() || ($this->user->getId() == $idUser)) {
            $attending = $this->participantModel->isParticipating($idEvent, $idUser);
            if ($attending) {
                $this->send(HTTPCodes::BAD_REQUEST, null, "User $idUser is already attending event $idEvent");
            } else {
                $id = $this->participantModel->add($idEvent, $idUser);

                if ($id == -1) {
                    return $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, null, "Error while adding participant to an event");
                }

                $this->send(HTTPCodes::OK, ["id" => $id], "User now participating in event");
            }
        }
    }

    public function remove(int $idEvent, int $idUser) {
        if ($this->user->isAdmin() || $this->user->isDeveloper() || ($this->user->getId() == $idUser)) {
            $attending = $this->participantModel->isParticipating($idEvent, $idUser);
            if (!$attending) {
                $this->send(HTTPCodes::BAD_REQUEST, null, "User $idUser is already attending Event $idEvent");
            } else {
                $status = $this->participantModel->remove($idEvent, $idUser);

                if (!$status) {
                    return $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, null, "Error while removing participant to an event");
                }

                $this->send(HTTPCodes::OK, null, "User no longer participating in event");
            }
        }
    }
}
