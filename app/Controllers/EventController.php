<?php

namespace App\Controllers;

use App\Utils\HTTPCodes;
use CodeIgniter\Files\File;

class EventController extends BaseController {
    private $eventModel;

    public function __construct() {
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

        $validation->setRules([
            "id" => "required|integer",
            "reason" => "required|max_length[50]"
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
        } else {
            $data = $this->request->getJSON(true);

            $this->eventModel->cancel($data["id"], $data["reason"]);

            $this->send(HTTPCodes::OK, $data, "Event canceled");
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
            $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
        } else {
            $data = $this->request->getJSON(true);

            if (isset($data["canceled"])) { // We don't want to update the canceled status with this method
                unset($data["canceled"]);
            }

            $this->eventModel->updateData($data);

            $this->send(HTTPCodes::OK, $data, "Event updated");
        }
    }

    public function add(): void {
        $validation =  \Config\Services::validation();

        $validation->setRules([
            "zip" => "required|max_length[5]",
            "dateDebut" => "required|valid_date[Y-m-d H:i:s]",
            "dateFin" => "required|valid_date[Y-m-d H:i:s]",
            "title" => "required|max_length[20]",
            "description" => "required|max_length[1000]"
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
        } else {
            $data = $this->request->getJSON(true);

            $this->eventModel->add($data);

            $this->send(HTTPCodes::OK, $data, "Event added");
        }
    }

    public function addImage(int $id): void {
        if ($this->eventModel->getById($id) == null) {
            $this->send(HTTPCodes::BAD_REQUEST, null, "Event with id $id does not exist");
            return;
        }

        $validation =  \Config\Services::validation();

        $validation->setRules([
            "image" => [
                "label" => "Image",
                "rules" => [
                    "uploaded[image]",
                    "is_image[image]",
                    "ext_in[image,jpg,jpeg,png]"
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
        } else {
            $file = $this->request->getFile("image");

            if (!$file->hasMoved()) {
                // generate random name
                $newName = $file->getRandomName();
                $file->move(WRITEPATH . "uploads/images", $newName);

                // optimize image
                \Config\Services::image()
                    ->withFile(WRITEPATH . "uploads/images/" . $newName)
                    ->save(WRITEPATH . "uploads/images/" . $newName, 30);

                // save image name in database
                $this->eventModel->addImage($id, $newName);

                $this->send(HTTPCodes::OK, ["file" => $newName], "Image uploaded");

            } else {
                $this->send(HTTPCodes::BAD_REQUEST, null, "Error", "The file has already been moved");
            }
        }
    }
}
