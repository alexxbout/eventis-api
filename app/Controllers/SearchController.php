<?php

namespace App\Controllers;

use App\Models\SearchModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Utils\HTTPCodes;
use Psr\Log\LoggerInterface;

class SearchController extends BaseController
{

    private const NO_CONTENT                = "Rien n'a été trouvé";
    private const RESULTATS                = "Voici les resultats de la recherche ";

    private SearchModel $searchModel;


    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->searchModel = new SearchModel();
    }

    public function getSearch(string $value)
    {
        if (strlen($value) < 3) {
            return;
        } else {
            $data = $this->searchModel->search($value);
            if (empty($data)) {
                $this->send(HTTPCodes::NO_CONTENT, $data, self::NO_CONTENT);
            } else {
                $this->send(HTTPCodes::OK, $data, self::RESULTATS . $value);
            }
        }
    }
}
