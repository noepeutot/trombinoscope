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

        $insertData = [];
        $insertDataResp = [];
        $insertDataMail = [];

        $personnels = $this->getDataFromURLAndID('personnels');
        $personnes = $this->getDataFromURLAndID('personnes');
        $localisation = $this->getDataFromURLAndID('localisation_personnels');
        $responsabilites = $this->getResponsabilitesFromID('personne_responsabilites');
        $sejours = $this->getSejourFromID('sejours');

        if (isset($personnes)) {
            $data['personnes'] = $personnes;

            $insertData['id_personne'] = $personnes['id_personne'];
            $insertData['nom'] = $personnes['nom_usage'];
            $insertData['prenom'] = $personnes['prenom'];

            $insertDataMail = $personnes['mails_pro'];
        }

        if (isset($personnels)) {
            $data['personnels'] = $personnels;

            $insertData['statut'] = $personnels['statut'];
            $insertData['equipe'] = $personnels['equipes'];
        }

        if (isset($localisation)) {
            $data['localisation'] = $localisation;

            $insertData['telephone'] = $localisation['tel_professionnel'];
            $insertData['numero_bureau'] = $localisation['numero_bureau'];
        }

        if (isset($responsabilites)) {
            $data['responsabilites'] = $responsabilites;

            $insertDataResp = $responsabilites;
        }

        if (isset($sejours)) {
            $data['sejours'] = $sejours;

            if (isset($sejours['encadrants'])) {
                $this->beautifulPrint($sejours['encadrants']);

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

            if (isset($sejours['stage']['sujet_stage'])) {
                $data['sujet'] = $sejours['stage']['sujet_stage'];
                $insertData['sujet'] = $sejours['stage']['sujet_stage'];
            } else if (isset($sejours['these']['sujet_these'])) {
                $data['sujet'] = $sejours['these']['sujet_these'];
                $insertData['sujet'] = $sejours['these']['sujet_these'];
            }
        }


        // TODO : A changer, il est préférable de stocker image par image
        // Vérifie si la pesonne possède une photo, sinon lui attribut une photo par défaut
        if (isset($personnes['photo'])) {
            file_put_contents('assets/images/profile_picture.jpg', file_get_contents($personnes['photo']));
            $data['imageURL'] = 'profile_picture.jpg';
        } else {
            $data['imageURL'] = 'default_profile.jpg';
        }


        $this->updatePersonneDB($insertData);
        $this->updateResponsabiliteDB($insertDataResp);
        $this->updateMailDB($insertDataMail);

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

    public function updatePersonneDB($personne)
    {
        if (!empty($personne)) {
            $db = db_connect();
            $builder = $db->table('personne');
            $query = $builder->select('id_personne')
                ->where('id_personne', $this->id)
                ->get();
            $builder->set($personne)
                ->where('id_personne', $personne['id_personne']);
            if ($query->getNumRows() == 0) {
                $builder->insert();
            } else {
                $builder->update();
            }
            $db->close();
        }
    }

    public function updateResponsabiliteDB($responsabilites)
    {
        if (!empty($responsabilites)) {
            $db = db_connect();
            $builder = $db->table('responsabilite');

            foreach ($responsabilites as $responsabilite) {
                $insert = [
                    'id_responsabilite' => $responsabilite['id_personne_resp'],
                    'libelle' => $responsabilite['responsabilite']['responsabilite'],
                    'id_personne' => $responsabilite['personne']['id_personne']
                ];
                $query = $builder->select()
                    ->where('id_responsabilite', $responsabilite['id_personne_resp'])
                    ->where('libelle', $responsabilite['responsabilite']['responsabilite'])
                    ->where('id_personne', $responsabilite['personne']['id_personne'])
                    ->get();
                $builder->set($insert)->where('id_responsabilite', $responsabilite['id_personne_resp']);
                if ($query->getNumRows() === 0) {
                    $builder->insert();
                } else {
                    $builder->update();
                }
                $db->close();
            }
        }
    }

    public function updateMailDB($mails)
    {
        if (!empty($mails)) {
            $db = db_connect();
            $builder = $db->table('mail');

            foreach ($mails as $mail) {
                $insert = [
                    'id_mail' => $mail['id_mail'],
                    'libelle' => $mail['mail'],
                    'type' => $mail['type_mail'],
                    'id_personne' => $this->id
                ];

                $query = $builder->select()
                    ->where('id_mail', $mail['id_mail'])
                    ->where('libelle', $mail['mail'])
                    ->where('type', $mail['type_mail'])
                    ->where('id_personne', $this->id)
                    ->get();

                $builder->set($insert)->where('id_mail', $mail['id_mail']);

                if ($query->getNumRows() === 0) {
                    $builder->insert();
                } else {
                    $builder->update();
                }
                $db->close();
            }
        }
    }

    public function updateResponsable($encadrants) {

    }

    public function beautifulPrint($data)
    {
        print("<pre>" . print_r($data, true) . "</pre>");
    }
}