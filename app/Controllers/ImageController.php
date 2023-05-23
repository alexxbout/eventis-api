<?php

namespace App\Controllers;

use App\Utils\HTTPCodes;

class ImageController extends BaseController {
    public function getUserImage(string $folder, string $imageName) {
        $imagePath = WRITEPATH . "uploads/images/" . $folder . "/" . $imageName;

        if (file_exists($imagePath)) {
            $this->response->setHeader("Content-Type", "image/jpeg"); // Modifier le type de contenu en fonction de votre type d'image
            $this->response->setHeader("Content-Length", filesize($imagePath));

            readfile($imagePath);
        } else {
            // Gérer le cas où l'image n"existe pas
            $this->send(HTTPCodes::NO_CONTENT, null, "Image not found");
        }
    }
}
