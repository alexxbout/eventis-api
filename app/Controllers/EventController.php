<?php

namespace App\Controllers;

use App\Models\EventModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Utils\HTTPCodes;
use Psr\Log\LoggerInterface;

class EventController extends BaseController {

    private EventModel $eventModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        parent::initController($request, $response, $logger);
        $this->eventModel = new EventModel();
    }

    public function getAll() {
        // Check if account should see archived events
        if ($this->user->isDeveloper() || $this->user->isAdmin() || $this->user->isEducator()) {
            $this->send(HTTPCodes::OK, $this->eventModel->getAll(), "OK");
        } else { // Account should see ONLY non-canceled events
            $this->send(HTTPCodes::OK, $this->eventModel->getAllNotCanceled(), "OK");
        }
    }

    public function getById(int $id) {
        // Check if account should see archived events
        if ($this->user->isDeveloper() || $this->user->isAdmin() || $this->user->isEducator()) {
            $this->send(HTTPCodes::OK, $this->eventModel->getById($id), "OK");
        } else { // Account should see ONLY non-canceled event
            $this->send(HTTPCodes::OK, $this->eventModel->getByIdNotCanceled($id), "OK");
        }
    }

    public function getByZip(string $zip) {
        // Check if account should see archived events
        if ($this->user->isDeveloper() || $this->user->isAdmin() || $this->user->isEducator()) {
            $this->send(HTTPCodes::OK, $this->eventModel->getByZip($zip), "OK");
        } else { // Account should see ONLY non-canceled events
            $this->send(HTTPCodes::OK, $this->eventModel->getByZipNotCanceled($zip), "OK");
        }
    }

    public function cancel($idEvent) {
        // Account can cancel event
        if ($this->user->isDeveloper() || $this->user->isAdmin() || ($this->user->isEducator() && $this->user->getIdFoyer() == $this->eventModel->getIdFoyerByIdEvent($idEvent))) {

            $data = $this->eventModel->getById($idEvent);

            if ($data == NULL) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, "Event does not exist");
            }

            if ($data->canceled == 1) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, "Event already cancelled");
            }

            $validation =  \Config\Services::validation();
            $validation->setRuleGroup("event_cancel_validation");

            if (!$validation->withRequest($this->request)->run()) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
            }

            $data = $this->request->getJSON();

            $this->eventModel->cancel($data->id, $data->reason);

            $this->send(HTTPCodes::OK, $data, "Event canceled");
        } else { // Account is unauthorized to cancel an event
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }

    public function uncancel($idEvent) {
        // Account can uncancel event
        if ($this->user->isDeveloper() || $this->user->isAdmin() || ($this->user->isEducator() && $this->user->getIdFoyer() == $this->eventModel->getIdFoyerByIdEvent($idEvent))) {

            $data = $this->eventModel->getById($idEvent);

            if ($data == NULL) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, "Event does not exist");
            }

            if ($data->canceled == 0) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, "Event is not cancelled");
            }

            $validation =  \Config\Services::validation();
            $validation->setRuleGroup("event_uncancel_validation"); // Validation the same as cancel

            if (!$validation->withRequest($this->request)->run()) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
            }

            $data = $this->request->getJSON();

            $this->eventModel->uncancel($data->id);

            $this->send(HTTPCodes::OK, $data, "Event cancelled");
        } else { // Account is unauthorized to uncancel an event
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }

    public function updateData(int $idEvent) {
        if ($this->user->isDeveloper() || $this->user->isAdmin() || ($this->user->isEducator() && $this->user->getIdFoyer() == $this->eventModel->getIdFoyerByIdEvent($idEvent))) {
            $validation =  \Config\Services::validation();

            $validation->setRuleGroup("event_update_validation");

            if (!$validation->withRequest($this->request)->run()) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
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

            $this->send(HTTPCodes::OK, $data, "Event updated");
        } else {
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }

    public function add() {
        if ($this->user->isDeveloper() || $this->user->isAdmin() || $this->user->isEducator()) {
            $validation =  \Config\Services::validation();

            $validation->setRuleGroup("event_add_validation");

            if (!$validation->withRequest($this->request)->run()) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
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

            $this->send(HTTPCodes::OK, $data, "Event added");
        } else {
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }

    public function addImage(int $idEvent) {
        if ($this->user->isDeveloper() || $this->user->isAdmin() || ($this->user->isEducator() && $this->user->getIdFoyer() == $this->eventModel->getIdFoyerByIdEvent($idEvent))) {
            if ($this->eventModel->getById($idEvent) == null) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, "Event with id $idEvent does not exist");
            }

            $validation =  \Config\Services::validation();
            $validation->setRuleGroup("event_addImage_validation");

            if (!$validation->withRequest($this->request)->run()) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
            }

            $file = $this->request->getFile("image");

            if ($file->hasMoved()) {
                $this->send(HTTPCodes::BAD_REQUEST, null, "Error", "The file has already been moved");
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

                $this->send(HTTPCodes::OK, ["file" => $newName], "Image uploaded");
            }
        } else {
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }
}
