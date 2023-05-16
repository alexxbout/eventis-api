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

    public function getAll(): void {
        //check if account should see archived events
        if($this->user->isDeveloper() || $this->user->isAdmin() || $this->user->isEducator()){
            $data = $this->eventModel->getAll();
            if ($data != null) {
                $this->send(HTTPCodes::OK, $data, "OK");
            } else {
                $this->send(HTTPCodes::NO_CONTENT);
            }
        } else { //account should see ONLY non-canceled events
            $data = $this->eventModel->getAllNotCanceled();
            if ($data != null) {
                $this->send(HTTPCodes::OK, $data, "OK");
            } else {
                $this->send(HTTPCodes::NO_CONTENT);
            }
        }
    }

    public function getById(int $id): void {
        //check if account should see archived events
        if($this->user->isDeveloper() || $this->user->isAdmin() || $this->user->isEducator()){
            $data = $this->eventModel->getById($id);
            if ($data != null) {
                $this->send(HTTPCodes::OK, $data, "OK");
            } else {
                $this->send(HTTPCodes::NO_CONTENT);
            }
        } else {//account should see ONLY non-canceled event
            $data = $this->eventModel->getByIdNotCanceled($id);
            if ($data != null) {
                $this->send(HTTPCodes::OK, $data, "OK");
            } else {
                $this->send(HTTPCodes::NO_CONTENT);
            }
        }
    }

    public function getByZip(string $zip): void {
        //check if account should see archived events
        if($this->user->isDeveloper() || $this->user->isAdmin() || $this->user->isEducator()){
            $data = $this->eventModel->getByZip($zip);
            if ($data != null) {
                $this->send(HTTPCodes::OK, $data, "OK");
            } else {
                $this->send(HTTPCodes::NO_CONTENT);
            }
        } else {//account should see ONLY non-canceled events
            $data = $this->eventModel->getByZipNotCanceled($zip);
            if ($data != null) {
                $this->send(HTTPCodes::OK, $data, "OK");
            } else {
                $this->send(HTTPCodes::NO_CONTENT);
            }
        }
    }

    public function cancel($idEvent) {
        //account can cancel event
        if($this->user->isDeveloper() || $this->user->isAdmin() || 
        ($this->user->isEducator() && $this->user->getIdFoyer() == $this->eventModel->getIdFoyerByIdEvent($idEvent))){
            
            $data = $this->eventModel->getById($idEvent);

            if($data == NULL) return $this->send(HTTPCodes::BAD_REQUEST, null, "Event does not exist"); 

            if($data["canceled"] == 1) return $this->send(HTTPCodes::BAD_REQUEST, null, "Event already cancelled");
            
            $validation =  \Config\Services::validation();

            $validation->setRuleGroup("event_cancel_validation");

            if (!$validation->withRequest($this->request)->run()) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
            }

            $data = $this->request->getJSON(true);

            $this->eventModel->cancel($data["id"], $data["reason"]);

            $this->send(HTTPCodes::OK, $data, "Event canceled");
        } else {//account is unauthorized to cancel an event
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }

    public function uncancel($idEvent) {
        //account can uncancel event
        if($this->user->isDeveloper() || $this->user->isAdmin() || 
        ($this->user->isEducator() && $this->user->getIdFoyer() == $this->eventModel->getIdFoyerByIdEvent($idEvent))){

            $data = $this->eventModel->getById($idEvent);

            if($data == NULL) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, "Event does not exist");
            }
            
            if($data["canceled"] == 0) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, "Event is not cancelled");
            }

            $validation =  \Config\Services::validation();

            $validation->setRuleGroup("event_uncancel_validation"); //validation the same as cancel

            if (!$validation->withRequest($this->request)->run()) {
                return $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
            }

            $data = $this->request->getJSON(true);

            $this->eventModel->uncancel($data["id"]);

            $this->send(HTTPCodes::OK, $data, "Event cancelled");
        } else {//account is unauthorized to uncancel an event
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }

    public function updateData(int $idEvent): void {
        if($this->user->isDeveloper() || $this->user->isAdmin() || 
        ($this->user->isEducator() && $this->user->getIdFoyer() == $this->eventModel->getIdFoyerByIdEvent($idEvent))){
            $validation =  \Config\Services::validation();

            $validation->setRuleGroup("event_update_validation");

            if (!$validation->withRequest($this->request)->run()) {
                $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
                return;
            }

            $data = $this->request->getJSON(true);

            if (isset($data["canceled"])) { // We don't want to update the canceled status with this method
                unset($data["canceled"]);
            }

            if (isset($data["id"])) { // We don't want to update the ID with this method
                unset($data["id"]);
            }

            if (isset($data["idFoyer"])) { // We don't want to update the creator ID with this method
                unset($data["idFoyer"]);
            }

            $this->eventModel->updateData($data);

            $this->send(HTTPCodes::OK, $data, "Event updated");
        } else {
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }

    public function add(): void {
        if($this->user->isDeveloper() || $this->user->isAdmin() || $this->user->isEducator()){
            $validation =  \Config\Services::validation();

            $validation->setRuleGroup("event_add_validation");

            if (!$validation->withRequest($this->request)->run()) {
                $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
                return;
            }

            $data = $this->request->getJSON(true);

            if (isset($data["id"])) { // We don't want to update the id with this method
                unset($data["id"]);
            }

            if (isset($data["canceled"])) { // We don't want to update the canceled status with this method
                unset($data["canceled"]);
            }
            
            $data["idFoyer"] = $this->user->getIdFoyer(); //creator = the person who makes the request

            $this->eventModel->add($data);

            $this->send(HTTPCodes::OK, $data, "Event added");
        } else {
            $this->send(HTTPCodes::UNAUTHORIZED);
        }
    }

    public function addImage(int $idEvent): void {
        if($this->user->isDeveloper() || $this->user->isAdmin() || 
        ($this->user->isEducator() && $this->user->getIdFoyer() == $this->eventModel->getIdFoyerByIdEvent($idEvent))){
            if ($this->eventModel->getById($idEvent) == null) {
                $this->send(HTTPCodes::BAD_REQUEST, null, "Event with id $idEvent does not exist");
                return;
            }

            $validation =  \Config\Services::validation();

            $validation->setRuleGroup("event_addImage_validation");

            if (!$validation->withRequest($this->request)->run()) {
                $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
                return;
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
