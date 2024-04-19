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
        $data[] = [];
        $data['personnes'] = null;
        $data['personnels'] = null;
        $data['localisation'] = null;

        $personnels = $this->getDataFromURLAndID('personnels');
        $personnes = $this->getDataFromURLAndID('personnes');
        $localisation = $this->getDataFromURLAndID('localisation_personnels');
        $responsabilites = $this->getResponsabilitesFromID('responsabilites_en_cours');
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

//        print("<pre>" . print_r($sejours, true) . "</pre>");

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
                }
            }
        }

        // Vérifie si la pesonne possède une photo, sinon lui attribut une photo par défaut
        if (isset($personnes) && isset($personnes['photo'])) {
            file_put_contents('assets/images/profile_picture.jpg', file_get_contents($personnes['photo']));
        } else {
            file_put_contents('assets/images/profile_picture.jpg', file_get_contents('assets/images/pp.jpg'));
        }

        return view('profile', $data);
    }

    /**
     * Fonction qui permet de faire une requête à l'API sur une route
     *  et récupérer les informations générales avec l'ID de la personne
     * @param $url "de l'API"
     * @return mixed|null
     */
    public function getDataFromURLAndID(string $url)
    {
        $resultat = $this->ApiModel->getDataFromURL($url);

        if (isset($resultat)) {
            // Cherche l'index si le personnel existe à avec l'$id correspondant à l'id_personne
            $personneKey = array_search($this->id, array_column($resultat, 'id_personne'));
            if ($personneKey !== false) {
                return $resultat[$personneKey];
            }
        }
        return null;
    }

    public function getResponsabilitesFromID(string $url)
    {
        $resultat = $this->ApiModel->getDataFromURL($url);

        $responsabilites = null;
        if (isset($resultat)) {
            foreach ($resultat as $result) {
                if ($result['id_personne'] === $this->id) {
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
}