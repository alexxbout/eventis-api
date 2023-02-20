<?php

namespace App\Controllers;

use App\Utils\HTTPCodes;

class EventController extends BaseController {
    private $eventModel;

    public function __construct() {
        $this->eventModel = new \App\Models\EventModel();
    }

    public function getAll(): void {
        $data = $this->eventModel->getAll();
        if ($data != null) {
            $this->send(HTTPCodes::OK, $data);
        } else {
            $this->send(HTTPCodes::NO_CONTENT, []);
        }
    }
    
    public function getById(int $id): void {
        $data = $this->eventModel->getById($id);
        if ($data != null) {
            $this->send(HTTPCodes::OK, $data);
        } else {
            $this->send(HTTPCodes::NO_CONTENT, []);
        }
    }

    public function getByZip(string $zip): void {
        $data = $this->eventModel->getByZip($zip);
        if ($data != null) {
            $this->send(HTTPCodes::OK, $data);
        } else {
            $this->send(HTTPCodes::NO_CONTENT, []);
        }
    }

    public function cancel(): void {
        $validation =  \Config\Services::validation();

        $validation->setRules([
            "id" => "required|integer",
            "reason" => "required|max_length[50]"
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $this->send(HTTPCodes::BAD_REQUEST, $validation->getErrors());
        } else {
            $data = $this->request->getJSON(true);

            $this->eventModel->cancel($data["id"], $data["reason"]);

            $this->send(HTTPCodes::OK, ["message" => "Event canceled", "data" => $data]);
        }
    }

    public function updateData(): void {
        $validation =  \Config\Services::validation();

        $validation->setRules([
            "id" => "required|integer",
            "dateDebut" => "permit_empty|valid_date[Y-m-d H:i:s]",
            "dateFin" => "permit_empty|valid_date[Y-m-d H:i:s]",
            "title" => "permit_empty|max_length[20]",
            "description" => "permit_empty|max_length[1000]",
            "pic" => "permit_empty|max_length[50]"
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $this->send(HTTPCodes::BAD_REQUEST, $validation->getErrors());
        } else {
            $data = $this->request->getJSON(true);

            if (isset($data["canceled"])) { // We don't want to update the canceled status with this method
                unset($data["canceled"]);
            }

            $this->eventModel->updateData($data);

            $this->send(HTTPCodes::OK, ["message" => "Event updated", "data" => $data]);
        }
    }

    public function add(): void {
        $validation =  \Config\Services::validation();

        $validation->setRules([
            "zip" => "required|max_length[5]",
            "dateDebut" => "required|valid_date[Y-m-d H:i:s]",
            "dateFin" => "required|valid_date[Y-m-d H:i:s]",
            "title" => "required|max_length[20]",
            "description" => "required|max_length[1000]",
            "pic" => "permit_empty|max_length[50]"
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $this->send(HTTPCodes::BAD_REQUEST, $validation->getErrors());
        } else {
            $data = $this->request->getJSON(true);

            $id = $this->eventModel->add($data);

            $this->send(HTTPCodes::OK, ["message" => "Event added", "id" => $id, "data" => $data]);
        }
    }
}
