<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Utils\HTTPCodes;
use Psr\Log\LoggerInterface;

class EventController extends BaseController {
    private $eventModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        parent::initController($request, $response, $logger);
        $this->eventModel = new \App\Models\EventModel();
    }

    public function getAll(): void {
        $data = $this->eventModel->getAll();
        if ($data != null) {
            $this->send(HTTPCodes::OK, $data, "OK");
        } else {
            $this->send(HTTPCodes::NO_CONTENT);
        }
    }

    public function getById(int $id): void {
        $data = $this->eventModel->getById($id);
        if ($data != null) {
            $this->send(HTTPCodes::OK, $data, "OK");
        } else {
            $this->send(HTTPCodes::NO_CONTENT);
        }
    }

    public function getByZip(string $zip): void {
        $data = $this->eventModel->getByZip($zip);
        if ($data != null) {
            $this->send(HTTPCodes::OK, $data, "OK");
        } else {
            $this->send(HTTPCodes::NO_CONTENT);
        }
    }

    public function cancel(): void {
        $validation =  \Config\Services::validation();

        $validation->setRuleGroup("event_cancel_validation");

        if (!$validation->withRequest($this->request)->run()) {
            $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
            return;
        }

        $data = $this->request->getJSON(true);

        $this->eventModel->cancel($data["id"], $data["reason"]);

        $this->send(HTTPCodes::OK, $data, "Event canceled");
    }

    public function updateData(): void {
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

        $this->eventModel->updateData($data);

        $this->send(HTTPCodes::OK, $data, "Event updated");
    }

    public function add(): void {
        $validation =  \Config\Services::validation();

        $validation->setRuleGroup("event_add_validation");

        if (!$validation->withRequest($this->request)->run()) {
            $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
            return;
        }

        $data = $this->request->getJSON(true);

        $this->eventModel->add($data);

        $this->send(HTTPCodes::OK, $data, "Event added");
    }

    public function addImage(int $id): void {
        if ($this->eventModel->getById($id) == null) {
            $this->send(HTTPCodes::BAD_REQUEST, null, "Event with id $id does not exist");
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
            $this->eventModel->addImage($id, $newName);

            $this->send(HTTPCodes::OK, ["file" => $newName], "Image uploaded");
        }
    }
}
