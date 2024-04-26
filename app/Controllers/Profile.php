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
        $insertPersonnels = [];
        $insertResponsabilites = [];
        $insertMails = [];
        $insertDataSejours = [];
        $insertEmployeurs = [];
        $insertEmployeurSejours = [];
        $insertLocalisations = [];
        $insertEncadrants = [];

        $personnels = $this->getDataFromURLAndID('personnels');
        $personnes = $this->getDataFromURLAndID('personnes');
        $localisation = $this->getDataFromURLAndID('localisation_personnels');
        $responsabilites = $this->getResponsabilitesFromID('personne_responsabilites');
        $sejours = $this->getSejourFromID('sejours');

        $allEncadrants = $this->getAllDataFromURL('encadrants');
        $allLocalisations = $this->getAllDataFromURL('localisation_personnels');
        $allPersonnels = $this->getAllDataFromURL('personnels');
        $allSejours = $this->getAllDataFromURL('sejours');
        $allMails = $this->getAllDataFromURL('mails_pro');
        $allResponsabilites = $this->getAllDataFromURL('personne_responsabilites');
        $allEmployeurs = $this->getAllDataFromURL('org_payeurs');
        $allEmployeur_sejour = $this->getAllDataFromURL('financements');

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

        if (isset($allPersonnels)) {
            $insertPersonnels = $allPersonnels;
        }

        if (isset($allLocalisations)) {
            $insertLocalisations = $allLocalisations;
        }

        if (isset($allResponsabilites)) {
            $insertResponsabilites = $allResponsabilites;
        }

        if (isset($allEmployeurs)) {
            $insertEmployeurs = $allEmployeurs;
        }

        if (isset($allEmployeur_sejour)) {
            $insertEmployeurSejours = $allEmployeur_sejour;
        }

        if (isset($allMails)) {
            $insertMails = $allMails;
        }

        if (isset($allSejours)) {
            $insertDataSejours = $allSejours;
        }

        if (isset($allEncadrants)) {
            $insertEncadrants = $allEncadrants;
        }

        if (isset($sejours)) {
            $data['sejours'] = $sejours;

            // TODO : Faire les encadrants pour la BDD
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


        // TODO : A changer, il est préférable de stocker image par image
        // Vérifie si la pesonne possède une photo, sinon lui attribut une photo par défaut
        if (isset($personnes['photo'])) {
            file_put_contents('assets/images/profile_picture.jpg', file_get_contents($personnes['photo']));
            $data['imageURL'] = 'profile_picture.jpg';
        } else {
            $data['imageURL'] = 'default_profile.jpg';
        }


        $this->updatePersonnelDB($insertPersonnels);
        $this->updateResponsabiliteDB($insertResponsabilites);
        $this->updateMailDB($insertMails);
        $this->updateLocalisationDB($insertLocalisations);
        $this->updateSejourBD($insertDataSejours);
        $this->updateEmployeurDB($insertEmployeurs);
        $this->updateEmployeurSejourDB($insertEmployeurSejours);
        $this->updateEncadrantDB($insertEncadrants);

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

    /**
     * Fonction qui permet de récupérer les données depuis l’API à partir de l’URL
     * @param $url
     * @return mixed
     */
    public function getAllDataFromURL($url)
    {
        return $this->ApiModel->getDataFromURL($url);
    }

    /**
     * Fonction de mise à jour de toutes les personnes en base de données
     * @param $personnesAPI
     * @return void
     */
    public function updatePersonnelDB($personnesAPI)
    {
        $db = db_connect();
        $builder = $db->table('personne');
        if (empty($personnesAPI)) {
            /**
             * contrainte de clé étrangère CASCADE mise sur PERSONNE  TODO : à migrer sur la production
             */
            $builder->delete();
        } else {
            $result = $builder->select()
                ->get()
                ->getResultArray();

            foreach ($result as $personne) {
                $personneKey = array_search($personne['id_personne'],
                    array_column($personnesAPI, 'id_personne'));
                if ($personneKey === false) {
                    $builder->where('id_personne', $personne['id_personne'])
                        ->delete();
                } else {
                    $data = [
                        'id_personne' => $personnesAPI[$personneKey]['id_personne'],
                        'nom' => $personnesAPI[$personneKey]['nom_usage'],
                        'prenom' => $personnesAPI[$personneKey]['prenom'],
                        'statut' => $personnesAPI[$personneKey]['statut'],
                        'equipe' => $personnesAPI[$personneKey]['equipes']
                    ];
                    $builder->set($data);
                    $builder->where('id_personne', $personne['id_personne'])
                        ->update();
                }
            }

            foreach ($personnesAPI as $personne) {
                $builder = $db->table('personne');
                $insert = [
                    'id_personne' => $personne['id_personne'],
                    'nom' => $personne['nom_usage'],
                    'prenom' => $personne['prenom'],
                    'statut' => $personne['statut'],
                    'equipe' => $personne['equipes']
                ];
                $query = $builder->select()
                    ->where('id_personne', $personne['id_personne'])
                    ->get();
                $builder->set($insert);
                if ($query->getNumRows() === 0) {
                    $builder->insert();
                }
            }
            $db->close();
        }
    }


    /**
     * Fonction de mise à jour de toutes les responsabilités en base de données
     * @param $responsabilitesArray
     * @return void
     */
    public function updateResponsabiliteDB($responsabilitesArray)
    {
        $db = db_connect();
        $builder = $db->table('responsabilite');

        if (empty($responsabilitesArray)) {
            $builder->delete();
        } else {
            $result = $builder->select()
                ->get()
                ->getResultArray();

            foreach ($result as $responsabiliteBDD) {
                $responsabiliteKey = array_search($responsabiliteBDD['id_responsabilite'],
                    array_column($responsabilitesArray, 'id_personne_resp'));
                if ($responsabiliteKey === false) {
                    $builder->where('id_responsabilite', $responsabiliteBDD['id_responsabilite'])
                        ->delete();
                } else {
                    $data = [
                        'id_responsabilite' => $responsabilitesArray[$responsabiliteKey]['id_personne_resp'],
                        'libelle' => $responsabilitesArray[$responsabiliteKey]['responsabilite']['responsabilite'],
                        'id_personne' => $responsabilitesArray[$responsabiliteKey]['personne']['id_personne']
                    ];
                    $builder->set($data);
                    $builder->where('id_responsabilite', $responsabiliteBDD['id_responsabilite'])
                        ->update();
                }
            }

            foreach ($responsabilitesArray as $responsabilite) {
                $builder = $db->table('responsabilite');
                $insert = [
                    'id_responsabilite' => $responsabilite['id_personne_resp'],
                    'libelle' => $responsabilite['responsabilite']['responsabilite'],
                    'id_personne' => $responsabilite['personne']['id_personne']
                ];
                $query = $builder->select()
                    ->where('id_responsabilite', $responsabilite['id_personne_resp'])
                    ->get();
                $builder->set($insert)->where('id_responsabilite', $responsabilite['id_personne_resp']);
                if ($query->getNumRows() === 0) {
                    $builder->insert();
                }
            }
            $db->close();
        }
    }

    /**
     * Fonction de mise à jour de tous les mails en base de données
     * @param $mailsArray
     * @return void
     */
    public function updateMailDB($mailsArray)
    {
        $db = db_connect();
        $builder = $db->table('mail');

        if (empty($mailsArray)) {
            $builder->delete();
        } else {
            $result = $builder->select()
                ->get()
                ->getResultArray();

            foreach ($result as $mailBDD) {
                $mailKey = array_search($mailBDD['id_mail'],
                    array_column($mailsArray, 'id_mail'));
                if ($mailKey === false) {
                    $builder->where('id_mail', $mailBDD['id_mail'])
                        ->delete();
                } else {
                    $data = [
                        'id_mail' => $mailsArray[$mailKey]['id_mail'],
                        'libelle' => $mailsArray[$mailKey]['mail'],
                        'type' => $mailsArray[$mailKey]['type_mail']['type_mail'],
                        'id_personne' => $mailsArray[$mailKey]['personne']['id_personne']
                    ];
                    $builder->set($data);
                    $builder->where('id_mail', $mailBDD['id_mail'])
                        ->update();
                }
            }

            foreach ($mailsArray as $mail) {
                $builder = $db->table('mail');
                $insert = [
                    'id_mail' => $mail['id_mail'],
                    'libelle' => $mail['mail'],
                    'type' => $mail['type_mail']['type_mail'],
                    'id_personne' => $mail['personne']['id_personne']
                ];

                $query = $builder->select()
                    ->where('id_mail', $mail['id_mail'])
                    ->get();

                $builder->set($insert)->where('id_mail', $mail['id_mail']);

                if ($query->getNumRows() === 0) {
                    $builder->insert();
                }

            }
            $db->close();
        }
    }

    /**
     * Fonction de mse à jour de tous les numéros de téléphone et numéros de bureau
     * @param $localisationsAPI
     * @return void
     */
    public function updateLocalisationDB($localisationsAPI)
    {
        $db = db_connect();
        $builder = $db->table('personne');

        if (empty($localisationsAPI)) {
            $update = [
                'telephone' => NULL,
                'numero_bureau' => NULL
            ];
            $builder->set($update)
                ->update();
        } else {
            foreach ($localisationsAPI as $localisation) {
                $builder = $db->table('personne');
                $update = [
                    'telephone' => $localisation['tel_professionnel'],
                    'numero_bureau' => $localisation['numero_bureau']
                ];

                $query = $builder->select()
                    ->where('id_personne', $localisation['id_personne'])
                    ->get();

                $builder->set($update)->where('id_personne', $localisation['id_personne']);
                if ($query->getNumRows() != 0) {
                    $builder->update();
                }
            }
            $db->close();
        }
    }

    /**
     * Fonction de mise à jour de tous les séjours en base de données
     * @param $sejourAPI
     * @return void
     */
    public function updateSejourBD($sejourAPI)
    {
        $db = db_connect();
        $builder = $db->table('sejour');
        if (empty($sejourAPI)) {
            /**
             * TRIGGER sur employeur_sejour mis en place lors du delete d'un séjour (TODO : à migrer sur la production)
             */
            $builder->delete();
        } else {
            $result = $builder->select()
                ->get()
                ->getResultArray();

            foreach ($result as $sejourBDD) {
                $sejourKey = array_search($sejourBDD['id_sejour'],
                    array_column($sejourAPI, 'id_sejour'));
                if ($sejourKey === false) {
                    $builder->where('id_sejour', $sejourBDD['id_sejour'])
                        ->delete();
                } else {
                    $update = [
                        'id_sejour' => $sejourAPI[$sejourKey]['id_sejour'],
                        'date_debut' => $sejourAPI[$sejourKey]['date_debut_sejour'],
                        'date_fin' => $sejourAPI[$sejourKey]['date_fin_sejour'],
                        'id_personne' => $sejourAPI[$sejourKey]['personne']['id_personne']
                    ];
                    if (isset($sejourAPI[$sejourKey]['these']['sujet_these'])) {
                        $update += ['sujet' => $sejourAPI[$sejourKey]['these']['sujet_these']];
                    } else if (isset($sejourAPI[$sejourKey]['stage']['sujet_stage'])) {
                        $update += ['sujet' => $sejourAPI[$sejourKey]['stage']['sujet_stage']];
                    }
                    $builder->set($update);
                    $builder->where('id_sejour', $sejourBDD['id_sejour'])
                        ->update();
                }
            }

            foreach ($sejourAPI as $sejour) {
                $builder = $db->table('sejour');
                $insert = [
                    'id_sejour' => $sejour['id_sejour'],
                    'date_debut' => $sejour['date_debut_sejour'],
                    'date_fin' => $sejour['date_fin_sejour'],
                    'id_personne' => $sejour['personne']['id_personne']
                ];
                if (isset($sejour['these']['sujet_these'])) {
                    $insert += ['sujet' => $sejour['these']['sujet_these']];
                } else if (isset($sejour['stage']['sujet_stage'])) {
                    $insert += ['sujet' => $sejour['stage']['sujet_stage']];
                }

                $query = $builder->select()
                    ->where('id_sejour', $sejour['id_sejour'])
                    ->get();

                $builder->set($insert)->where('id_sejour', $sejour['id_sejour']);

                if ($query->getNumRows() === 0) {
                    $builder->insert();
                }

            }
            $db->close();
        }
    }

    /**
     * Fonction de mise à jour de tous les employeurs en base de données
     * @param $employeursArray
     * @return void
     */
    public function updateEmployeurDB($employeursArray)
    {
        $db = db_connect();
        $builder = $db->table('employeur');

        if (empty($employeursArray)) {
            $builder->delete();
        } else {
            $result = $builder->select()
                ->get()
                ->getResultArray();

            foreach ($result as $employeurBDD) {
                $employeurKey = array_search($employeurBDD['id_employeur'],
                    array_column($employeursArray, 'id_org_payeur'));
                if ($employeurKey === false) {
                    $builder->where('id_employeur', $employeurBDD['id_employeur'])
                        ->delete();
                } else {
                    $data = [
                        'id_employeur' => $employeursArray[$employeurKey]['id_org_payeur'],
                        'nom' => $employeursArray[$employeurKey]['organisme_payeur'],
                        'nom_court' => $employeursArray[$employeurKey]['nom_court_op']
                    ];
                    $builder->set($data);
                    $builder->where('id_employeur', $employeurBDD['id_employeur'])
                        ->update();
                }
            }

            foreach ($employeursArray as $employeur) {
                $builder = $db->table('employeur');
                $insert = [
                    'id_employeur' => $employeur['id_org_payeur'],
                    'nom' => $employeur['organisme_payeur'],
                    'nom_court' => $employeur['nom_court_op']
                ];
                $query = $builder->select()
                    ->where('id_employeur', $employeur['id_org_payeur'])
                    ->get();
                $builder->set($insert)->where('id_employeur', $employeur['id_org_payeur']);
                if ($query->getNumRows() === 0) {
                    $builder->insert();
                }
            }
            $db->close();
        }
    }

    /**
     * Fonction de mise à jour de tous les employeur_sejour (liens entre séjours et employeurs) en base de données
     * @param $employeur_sejourArray
     * @return void
     */
    public function updateEmployeurSejourDB($employeur_sejourArray)
    {
        $db = db_connect();
        $builder = $db->table('employeur_sejour');

        if (empty($employeur_sejourArray)) {
            $builder->delete();
        } else {
            $result = $builder->select()
                ->get()
                ->getResultArray();

            // Parcours des éléments de la table employeur_sejour pour savoir si l’élément existe toujours sur l’API
            foreach ($result as $employeur_sejourBDD) {
                $hasEmployeurSejour = false;
                $indexEmployeurSejour = false;
                for ($i = 0; $i < sizeof($employeur_sejourArray); $i++) {
                    $indexEmployeurSejour = $i;
                    if ($employeur_sejourArray[$i]['id_sejour'] === $employeur_sejourBDD['id_sejour']
                        && $employeur_sejourArray[$i]['org_payeur']['id_org_payeur'] === $employeur_sejourBDD['id_employeur']) {
                        $hasEmployeurSejour = true;
                        break;
                    }
                }

                // Si on ne retrouve pas l’employeurSejour dans l’array, on le supprime de la base de données.
                if ($hasEmployeurSejour === false) {
                    $builder->where('id_sejour', $employeur_sejourBDD['id_sejour'])
                        ->where('id_employeur', $employeur_sejourBDD['id_employeur'])
                        ->delete();

                    // Sinon, on le met à jour avec les nouvelles données.
                } else {
                    $data = [
                        'id_employeur' => $employeur_sejourArray[$indexEmployeurSejour]['id_org_payeur'],
                        'nom' => $employeur_sejourArray[$indexEmployeurSejour]['organisme_payeur'],
                        'nom_court' => $employeur_sejourArray[$indexEmployeurSejour]['nom_court_op']
                    ];
                    $builder->set($data);
                    $builder->where('id_sejour', $employeur_sejourBDD['id_sejour'])
                        ->where('id_employeur', $employeur_sejourBDD['id_employeur'])
                        ->update();
                }
            }

            foreach ($employeur_sejourArray as $employeur_sejour) {
                $builder = $db->table('employeur_sejour');
                $insert = [
                    'id_sejour' => $employeur_sejour['id_sejour'],
                    'id_employeur' => $employeur_sejour['org_payeur']['id_org_payeur']
                ];
                $query = $builder->select()
                    ->where('id_sejour', $employeur_sejour['id_sejour'])
                    ->where('id_employeur', $employeur_sejour['org_payeur']['id_org_payeur'])
                    ->get();
                $builder->set($insert);
                if ($query->getNumRows() === 0) {
                    $builder->insert();
                }
            }
            $db->close();
        }
    }

    /**
     * Fonction de mise à jour de tous les encadrants en base de données
     * @param $encadrantsAPI
     * @return void
     */
    public function updateEncadrantDB($encadrantsAPI)
    {
        $db = db_connect();
        $builder = $db->table('encadrant');

        if (empty($encadrantsAPI)) {
            $builder->delete();
        } else {
            $result = $builder->select()
                ->get()
                ->getResultArray();

            foreach ($result as $encadrantBDD) {
                $encadrantKey = array_search($encadrantBDD['id_encadrant'],
                    array_column($encadrantsAPI, 'id_encadrant'));
                if ($encadrantKey === false) {
                    $builder->where('id_encadrant', $encadrantBDD['id_encadrant'])
                        ->delete();
                } else {
                    $update = [
                        'id_encadrant' => $encadrantsAPI[$encadrantKey]['id_encadrant'],
                        'id_sejour' => $encadrantsAPI[$encadrantKey]['id_sejour'],
                        'nom' => $encadrantsAPI[$encadrantKey]['nom'],
                        'prenom' => $encadrantsAPI[$encadrantKey]['prenom']
                    ];

                    if (isset($encadrantsAPI[$encadrantKey]['personne']['id_personne'])) {
                        $update += ['id_personne' => $encadrantsAPI[$encadrantKey]['personne']['id_personne']];
                    } else {
                        $update += ['id_personne' => NULL];
                    }

                    $builder->set($update);
                    $builder->where('id_encadrant', $encadrantBDD['id_encadrant'])
                        ->update();
                }
            }

            foreach ($encadrantsAPI as $encadrant) {
                $builder = $db->table('encadrant');
                $insert = [
                    'id_encadrant' => $encadrant['id_encadrant'],
                    'id_sejour' => $encadrant['id_sejour'],
                    'nom' => $encadrant['nom'],
                    'prenom' => $encadrant['prenom']
                ];

                if (isset($encadrant['personne']['id_personne'])) {
                    $insert += ['id_personne' => $encadrant['personne']['id_personne']];
                } else {
                    $insert += ['id_personne' => $encadrant['id_personne']];
                }

                $query = $builder->select()
                    ->where('id_encadrant', $encadrant['id_encadrant'])
                    ->get();
                $builder->set($insert);
                if ($query->getNumRows() === 0) {
                    $builder->insert();
                }
            }
            $db->close();
        }
    }

    public function beautifulPrint($data)
    {
        print("<pre>" . print_r($data, true) . "</pre>");
    }
}