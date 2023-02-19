<?php

namespace App\Controllers;

class EventController extends BaseController {
    private $eventModel;

    public function __construct() {
        $this->eventModel = new \App\Models\EventModel();
    }

    public function getAll(): void {
        $this->send(200, $this->eventModel->getAll());
    }
    
    public function getById(int $id): void {
        $this->send(200, $this->eventModel->getById($id));
    }

    public function getByZip(string $zip): void {
        $this->send(200, $this->eventModel->getByZip($zip));
    }

    public function cancel(): void {
        $validation =  \Config\Services::validation();

        $validation->setRules([
            "id" => "required|integer",
            "reason" => "required|max_length[50]"
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $this->send(400, $validation->getErrors());
        } else {
            $data = $this->stdClassToArray($this->request->getJSON());

            $this->eventModel->cancel($data["id"], $data["reason"]);

            $this->send(200, ["message" => "Event canceled", "data" => $data]);
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
            $this->send(400, $validation->getErrors());
        } else {
            $data = $this->stdClassToArray($this->request->getJSON());

            if (isset($data["canceled"])) {
                unset($data["canceled"]);
            }

            $this->eventModel->updateData($data);

            $this->send(200, ["message" => "Event updated", "data" => $data]);
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
            $this->send(400, $validation->getErrors());
        } else {
            $data = $this->stdClassToArray($this->request->getJSON());

            $id = $this->eventModel->add($data);

            $this->send(200, ["message" => "Event added", "id" => $id, "data" => $data]);
        }
    }
}
