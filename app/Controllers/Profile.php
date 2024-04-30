<?php

namespace App\Controllers;

use App\Models\APIModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Profile extends BaseController
{
    protected int $id;
    protected APIModel $ApiModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->ApiModel = new APIModel();
    }

    public function index($id)
    {
        $this->id = $id;
        $data = [];
        $data['personnes'] = null;
        $data['personnels'] = null;
        $data['localisation'] = null;

        // TODO : changer avec requête BDD
        $personnels = $this->getDataFromURLAndID('personnels');
        $personnes = $this->getDataFromURLAndID('personnes');
        $localisation = $this->getDataFromURLAndID('localisation_personnels');
        $responsabilites = $this->getResponsabilitesFromID('personne_responsabilites');
        $sejours = $this->getSejourFromID('sejours');

        if (isset($personnes)) {
            $data['personnes'] = $personnes;
        }

        if (isset($personnels)) {
            $data['personnels'] = $personnels;
        }

        if (isset($localisation)) {
            $data['localisation'] = $localisation;
        }

        if (isset($responsabilites)) {
            $data['responsabilites'] = $responsabilites;
        }

        if (isset($sejours)) {
            $data['sejours'] = $sejours;

            if (isset($sejours['encadrants'])) {
                foreach ($sejours['encadrants'] as $personne) {
                    if (isset($personne['personne'])) {
                        $encadrants[] = $personne['personne'];
                    } else {
                        $encadrants[] = $personne;
                    }
                }

                if (!is_null($encadrants)) {
                    $data['encadrants'] = $encadrants;
                    $insertDataEncadrants = $encadrants;
                }
            }

            if (isset($sejours['stage']['sujet_stage'])) {
                $data['sujet'] = $sejours['stage']['sujet_stage'];
            } else if (isset($sejours['these']['sujet_these'])) {
                $data['sujet'] = $sejours['these']['sujet_these'];
            }
        }

        return view('profile', $data);
    }

    /**
     * Fonction qui permet de faire une requête à l’API sur une route
     *  et récupérer les informations générales avec l’ID de la personne
     * @param $url "de l'API"
     * @return mixed|null
     */
    public function getDataFromURLAndID(string $url)
    {
        $resultat = $this->ApiModel->getDataFromURL($url);

        if (isset($resultat)) {
            // Cherche l’index si le personnel existe avec l'$id correspondant à l’id_personne
            $personneKey = array_search($this->id, array_column($resultat, 'id_personne'));
            if ($personneKey !== false) {
                return $resultat[$personneKey];
            }
        }
        return null;
    }

    /**
     * @param string $url
     * @return array
     */
    public function getResponsabilitesFromID(string $url)
    {
        $resultat = $this->ApiModel->getDataFromURL($url);

        $responsabilites = null;
        if (isset($resultat)) {
            foreach ($resultat as $result) {
                if (isset($result['personne']) && $result['personne']['id_personne'] === $this->id) {
                    $responsabilites[] = $result;
                }
            }
        }
        return $responsabilites;
    }

    /**
     * Fonction qui permet de faire une requête à l'API sur une route
     * et récupérer les informations du dernier séjour avec l'ID de la personne
     * @param $url "de l'API"
     * @return mixed|null
     */
    public function getSejourFromID(string $url)
    {
        $resultat = $this->ApiModel->getDataFromURL($url);
        $sejourKey = null;

        // TODO : faire le co-encadrement
        // TODO : faire le check si plusieurs séjours, prendre le dernier séjour
        if (isset($resultat)) {
            for ($i = 0; $i < count($resultat); $i++) {
                if ($resultat[$i]['personne']['id_personne'] === $this->id) {
                    $sejourKey = $i;
                }
            }
            if (isset($sejourKey)) {
                return $resultat[$sejourKey];
            }
        }
        return null;
    }

    /**
     * Fonction qui permet de récupérer les données depuis l’API à partir de l’URL
     * @param $url
     * @return mixed
     */
    public function getAllDataFromURL($url)
    {
        return $this->ApiModel->getDataFromURL($url);
    }

    public function beautifulPrint($data)
    {
        print("<pre>" . print_r($data, true) . "</pre>");
    }
}