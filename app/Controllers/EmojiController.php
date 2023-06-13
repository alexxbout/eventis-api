<?php

namespace App\Controllers;

use App\Models\EmojiModel;
use App\Utils\HTTPCodes;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class EmojiController extends BaseController {

    private const ALL_EMOJIS = "Tous les emojis";
    private const NO_CONTENT = "Aucun emoji trouvé";

    private EmojiModel $emojiModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        parent::initController($request, $response, $logger);
        $this->emojiModel = new EmojiModel();
    }

    public function getAll() {
        $data = $this->emojiModel->getAll();
        if (empty($data)) {
            $this->send(HTTPCodes::NO_CONTENT, $data, self::NO_CONTENT);
        } else {
            $data = array_map(static function ($emoji) {
                return $emoji->code;
            }, $data);

            $this->send(HTTPCodes::OK, $data, self::ALL_EMOJIS);
        }
    }
}
