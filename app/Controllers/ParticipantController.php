<?php

namespace App\Controllers;

use App\Models\ParticipantModel;
use App\Utils\HTTPCodes;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class ParticipantController extends BaseController {

    private const ALL_PARTICIPANTS              = "Tous les participants";
    private const PARTICIPANT_ADDED             = "Le participant a été ajouté";
    private const PARTICIPANT_REMOVED           = "Le participant a été supprimé";
    private const PARTICIPANT_ADD_ERROR         = "Erreur lors de l'ajout du participant";
    private const PARTICIPANT_ALREADY_ATTENDING = "Le participant est déjà inscrit à l'événement";
    private const PARTICIPANT_NOT_ATTENDING     = "Le participant n'est pas inscrit à l'événement";
    private const PARTICIPANT_REMOVE_ERROR      = "Erreur lors de la suppression du participant";

    private ParticipantModel $participantModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        parent::initController($request, $response, $logger);

        $this->participantModel = new ParticipantModel();
    }

    public function getAll(int $idEvent) {
        $this->send(HTTPCodes::OK, $this->participantModel->getAll($idEvent), self::ALL_PARTICIPANTS);
    }

    public function add(int $idEvent, int $idUser) {
        if ($this->user->isAdmin() || $this->user->isDeveloper() || ($this->user->getId() == $idUser)) {
            $attending = $this->participantModel->isParticipating($idEvent, $idUser);
            if ($attending) {
                $this->send(HTTPCodes::BAD_REQUEST, null, self::PARTICIPANT_ALREADY_ATTENDING);
            } else {
                $id = $this->participantModel->add($idEvent, $idUser);

                if ($id == -1) {
                    return $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, null, self::PARTICIPANT_ADD_ERROR);
                }

                $this->send(HTTPCodes::OK, ["id" => $id], self::PARTICIPANT_ADDED);
            }
        }
    }

    public function remove(int $idEvent, int $idUser) {
        if ($this->user->isAdmin() || $this->user->isDeveloper() || ($this->user->getId() == $idUser)) {
            $attending = $this->participantModel->isParticipating($idEvent, $idUser);
            if (!$attending) {
                $this->send(HTTPCodes::BAD_REQUEST, null, self::PARTICIPANT_NOT_ATTENDING);
            } else {
                $status = $this->participantModel->remove($idEvent, $idUser);

                if (!$status) {
                    return $this->send(HTTPCodes::INTERNAL_SERVER_ERROR, null, self::PARTICIPANT_REMOVE_ERROR);
                }

                $this->send(HTTPCodes::OK, null, self::PARTICIPANT_REMOVED);
            }
        }
    }
}
