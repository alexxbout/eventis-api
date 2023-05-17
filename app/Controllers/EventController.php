<?php

namespace App\Controllers;

use App\Models\EventModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Utils\HTTPCodes;
use Psr\Log\LoggerInterface;

class EventController extends BaseController {

    private const ALL_EVENTS                = "Tous les événements";
    private const ALL_NON_CANCELED_EVENTS   = "Tous les événements non annulés";
    private const EVENT_BY_ID               = "Evénement d'id ";
    private const EVENT_BY_ID_NOT_CANCELED  = "Evénement non annulé d'id ";
    private const EVENT_BY_ZIP              = "Evénements du code postal ";
    private const EVENT_BY_ZIP_NOT_CANCELED = "Evénements non annulés du code postal ";
    private const EVENT_DOES_NOT_EXIST      = "L'événement n'existe pas";
    private const EVENT_ALREADY_CANCELED    = "L'événement est déjà annulé";
    private const EVENT_CANCELED            = "L'événement a été annulé";
    private const EVENT_UNCANCELED          = "L'événement a été restauré";
    private const EVENT_UPDATED             = "L'événement a été mis à jour";
    private const EVENT_ADDED               = "L'événement a été ajouté";

    private const FILE_ALREADY_MOVED        = "Le fichier a déjà été déplacé";
    private const IMAGE_UPLOADED            = "L'image a été téléchargée";

    private const VALIDATION_ERROR          = "Erreur de validation";

    private EventModel $eventModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        parent::initController($request, $response, $logger);
        $this->eventModel = new EventModel();
    }

    public function getAll() {
        // Check if account should see archived events
        if ($this->user->isDeveloper() || $this->user->isAdmin() || $this->user->isEducator()) {
            $this->send(HTTPCodes::OK, $this->eventModel->getAll(), self::ALL_EVENTS);
        } else { // Account should see ONLY non-canceled events
            $this->send(HTTPCodes::OK, $this->eventModel->getAllNotCanceled(), self::ALL_NON_CANCELED_EVENTS);
        }
    }

    public function getById(int $id) {
        // Check if account should see archived events
        if ($this->user->isDeveloper() || $this->user->isAdmin() || $this->user->isEducator()) {
            $this->send(HTTPCodes::OK, $this->eventModel->getById($id), self::EVENT_BY_ID . $id);
        } else { // Account should see ONLY non-canceled event
            $this->send(HTTPCodes::OK, $this->eventModel->getByIdNotCanceled($id), self::EVENT_BY_ID_NOT_CANCELED . $id);
        }
    }

    public function getByZip(string $zip) {
        // Check if account should see archived events
        if ($this->user->isDeveloper() || $this->user->isAdmin() || $this->user->isEducator()) {
            $this->send(HTTPCodes::OK, $this->eventModel->getByZip($zip), self::EVENT_BY_ZIP . $zip);
        } else { // Account should see ONLY non-canceled events
            $this->send(HTTPCodes::OK, $this->eventModel->getByZipNotCanceled($zip), self::EVENT_BY_ZIP_NOT_CANCELED . $zip);
        }
    }

    public function cancel($idEvent) {
        // Account can cancel event
        if ($this->user->isDeveloper() || $this->user->isAdmin() || ($this->user->isEducator() && $this->user->getIdFoyer() == $this->eventModel->getIdFoyerByIdEvent($idEvent))) {

            $data = $this->eventModel->getById($idEvent);

            if ($data == NULL) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, self::EVENT_DOES_NOT_EXIST);
            }

            if ($data->canceled == 1) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, self::EVENT_ALREADY_CANCELED);
            }

            $validation =  \Config\Services::validation();
            $validation->setRuleGroup("event_cancel_validation");

            if (!$validation->withRequest($this->request)->run()) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, self::VALIDATION_ERROR, $validation->getErrors());
            }

            $data = $this->request->getJSON();

            $this->eventModel->cancel($data->id, $data->reason);

            $this->send(HTTPCodes::OK, $data, self::EVENT_CANCELED);
        } else { // Account is unauthorized to cancel an event
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }

    public function uncancel($idEvent) {
        // Account can uncancel event
        if ($this->user->isDeveloper() || $this->user->isAdmin() || ($this->user->isEducator() && $this->user->getIdFoyer() == $this->eventModel->getIdFoyerByIdEvent($idEvent))) {

            $data = $this->eventModel->getById($idEvent);

            if ($data == NULL) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, self::EVENT_DOES_NOT_EXIST);
            }

            if ($data->canceled == 0) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, self::EVENT_DOES_NOT_EXIST);
            }

            $validation =  \Config\Services::validation();
            $validation->setRuleGroup("event_uncancel_validation"); // Validation the same as cancel

            if (!$validation->withRequest($this->request)->run()) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, self::VALIDATION_ERROR, $validation->getErrors());
            }

            $data = $this->request->getJSON();

            $this->eventModel->uncancel($data->id);

            $this->send(HTTPCodes::OK, $data, self::EVENT_UNCANCELED);
        } else { // Account is unauthorized to uncancel an event
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }

    public function updateData(int $idEvent) {
        if ($this->user->isDeveloper() || $this->user->isAdmin() || ($this->user->isEducator() && $this->user->getIdFoyer() == $this->eventModel->getIdFoyerByIdEvent($idEvent))) {
            $validation =  \Config\Services::validation();

            $validation->setRuleGroup("event_update_validation");

            if (!$validation->withRequest($this->request)->run()) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, self::VALIDATION_ERROR, $validation->getErrors());
            }

            $data = $this->request->getJSON();

            if (isset($data->canceled)) { // We don't want to update the canceled status with this method
                unset($data->canceled);
            }

            if (isset($data->id)) { // We don't want to update the ID with this method
                unset($data->id);
            }

            if (isset($data->idFoyer)) { // We don't want to update the creator ID with this method
                unset($data->idFoyer);
            }

            $this->eventModel->updateData($data);

            $this->send(HTTPCodes::OK, $data, self::EVENT_UPDATED);
        } else {
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }

    public function add() {
        if ($this->user->isDeveloper() || $this->user->isAdmin() || $this->user->isEducator()) {
            $validation =  \Config\Services::validation();

            $validation->setRuleGroup("event_add_validation");

            if (!$validation->withRequest($this->request)->run()) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, self::VALIDATION_ERROR, $validation->getErrors());
            }

            $data = $this->request->getJSON();

            if (isset($data->id)) { // We don't want to update the id with this method
                unset($data->id);
            }

            if (isset($data->canceled)) { // We don't want to update the canceled status with this method
                unset($data->canceled);
            }

            $data->idFoyer = $this->user->getIdFoyer(); //creator = the person who makes the request

            $this->eventModel->add($data);

            $this->send(HTTPCodes::OK, $data, self::EVENT_ADDED);
        } else {
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }

    public function addImage(int $idEvent) {
        if ($this->user->isDeveloper() || $this->user->isAdmin() || ($this->user->isEducator() && $this->user->getIdFoyer() == $this->eventModel->getIdFoyerByIdEvent($idEvent))) {
            if ($this->eventModel->getById($idEvent) == null) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, self::EVENT_DOES_NOT_EXIST);
            }

            $validation =  \Config\Services::validation();
            $validation->setRuleGroup("event_addImage_validation");

            if (!$validation->withRequest($this->request)->run()) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, self::VALIDATION_ERROR, $validation->getErrors());
            }

            $imageName = $this->eventModel->getImage($idEvent);
            if ($imageName != NULL) {
                // Check if image has already been uploaded with the same name
                if (file_exists(WRITEPATH . "uploads/images/" . $imageName)) {
                    unlink(WRITEPATH . "uploads/images/" . $imageName);
                }
            }

            $file = $this->request->getFile("image");

            if ($file->hasMoved()) {
                $this->send(HTTPCodes::BAD_REQUEST, null, self::FILE_ALREADY_MOVED);
            } else {
                // Generate random name
                $newName = $file->getRandomName();
                $file->move(WRITEPATH . "uploads/images", $newName);

                // Optimize image
                \Config\Services::image()
                    ->withFile(WRITEPATH . "uploads/images/" . $newName)
                    ->save(WRITEPATH . "uploads/images/" . $newName, 30);

                // Save image name in database
                $this->eventModel->addImage($idEvent, $newName);

                $this->send(HTTPCodes::OK, ["file" => $newName], self::IMAGE_UPLOADED);
            }
        } else {
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }
}
