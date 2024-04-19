<?php

namespace App\Controllers;

use App\Models\APIModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Home extends BaseController
{
    protected APIModel $ApiModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->ApiModel = new APIModel();
    }

    /**
     * @return string
     */
    public function index(): string
    {



        $personnels = $this->ApiModel->getDataFromURL('personnels');

        $data['statut'] = ['Chercheur', 'Doctorant', 'Enseignant-Chercheur'];
        $data['equipe'] = ['MAGE', 'MADEO', 'MADEA', 'SYREL'];
        $data['tuteur'] = ['COUSTEAU Eric', 'POLLET Colette', 'POLIZZI Rachelle', 'ARRIEULA Beatrice'];
        $data['personnels'] = $personnels;

        return view('home', $data);
    }

    /**
     * Function qui permet la recherche
     * @param $query
     * @return void
     */
    public function search($query) {

    }
}
