<?php

namespace App\Controllers;

use App\Models\APIModel;
use App\Models\PersonneModel;
use App\Models\ResponsabiliteModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use ReflectionException;

class Home extends BaseController
{
    protected array $personnels;

    protected array $allPersonnels;
    protected APIModel $ApiModel;

    protected PersonneModel $personneModel;

    protected ResponsabiliteModel $responsabiliteModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->ApiModel = new APIModel();
        $this->personneModel = new PersonneModel();
        $this->responsabiliteModel = new ResponsabiliteModel();
    }

    // TODO MODEL + style css fichier

    /**
     * @return string
     * @throws ReflectionException
     */
    public function index(): string
    {
        $this->updateDB();

        $this->allPersonnels = $this->getPersonnes();

        if ($this->request->getGet('q')
            || $this->request->getGet('statut[]')
            || $this->request->getGet('equipe[]')
            || $this->request->getGet('tuteur[]')) {
            $query = $this->request->getGet('q');
            $data['query'] = $query;
            $statut = $this->request->getGet('statut[]');
            $data['filtreStatut'] = $statut;
            $equipe = $this->request->getGet('equipe[]');
            $data['filtreEquipe'] = $equipe;
            $tuteur = $this->request->getGet('tuteur[]');
            $data['filtreTuteur'] = $tuteur;
            $this->personnels = $this->search($query, $statut, $equipe, $tuteur);
        } else {
            $this->personnels = $this->allPersonnels;
        }

        $data['statut'] = $this->getStatuts();
        $data['equipe'] = $this->getEquipes();
        $data['tuteur'] = $this->getEncadrants();
        $data['personnes'] = $this->personnels;
        $data['allPersonnels'] = $this->allPersonnels;

        return view('home', $data);
    }

    /**
     * Fonction de mise à jour de la base de données TODO : A automatiser !
     * @return void
     */
    public function updateDB()
    {
        $insertPersonnels = [];
        $insertResponsabilites = [];
        $insertMails = [];
        $insertDataSejours = [];
        $insertEmployeurs = [];
        $insertEmployeurSejours = [];
        $insertLocalisations = [];
        $insertEncadrants = [];
        $insertStatuts = [];
        $insertEquipes = [];
        $insertImages = [];

        $allEncadrants = $this->getAllDataFromURL('encadrants');
        $allLocalisations = $this->getAllDataFromURL('localisation_personnels');
        $allPersonnels = $this->getAllDataFromURL('personnels');
        $allSejours = $this->getAllDataFromURL('sejours');
        $allMails = $this->getAllDataFromURL('mails_pro');
        $allResponsabilites = $this->getAllDataFromURL('personne_responsabilites');
        $allEmployeurs = $this->getAllDataFromURL('org_payeurs');
        $allEmployeur_sejour = $this->getAllDataFromURL('financements');
        $allStatuts = $this->getAllDataFromURL('statuts');
        $allEquipes = $this->getAllDataFromURL('groupes');
        $allPersonnes = $this->getAllDataFromURL('personnes');

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

        if (isset($allStatuts)) {
            $insertStatuts = $allStatuts;
        }

        if (isset($allEquipes)) {
            $insertEquipes = $allEquipes;
        }

        if (isset($allPersonnes)) {
            foreach ($allPersonnes as $personne) {
                $insertImages[] = ['id_personne' => $personne['id_personne'],
                    'photo' => $personne['photo']];
            }
        }

        $this->updatePersonnelDB($insertPersonnels);
        $this->updateResponsabiliteDB($insertResponsabilites);
        $this->updateMailDB($insertMails);
        $this->updateLocalisationDB($insertLocalisations);
        $this->updateSejourBD($insertDataSejours);
        $this->updateEmployeurDB($insertEmployeurs);
        $this->updateEmployeurSejourDB($insertEmployeurSejours);
        $this->updateEncadrantDB($insertEncadrants);
        $this->updateStatusDB($insertStatuts);
        $this->updateEquipeDB($insertEquipes);
        $this->createProfilePictures($insertImages);
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
     * @param $personnelsAPI
     * @return void
     */
    public function updatePersonnelDB($personnelsAPI)
    {
        if (empty($personnelsAPI)) {
            /**
             * Contrainte de clé étrangère CASCADE mise sur PERSONNE
             */
            $this->personneModel->deleteAll();
        } else {
            $personnesBD = $this->personneModel->getAllPersonnes();

            foreach ($personnesBD as $personne) {
                $personneKey = array_search($personne->id_personne,
                    array_column($personnelsAPI, 'id_personne'));
                if ($personneKey === false) {
                    $this->personneModel->deletePersonne($personne->id_personne);
                } else {
                    $data = [
                        'nom' => mb_strtoupper($personnelsAPI[$personneKey]['nom_usage']),
                        'prenom' => $personnelsAPI[$personneKey]['prenom'],
                        'statut' => $personnelsAPI[$personneKey]['statut'],
                        'equipe' => $personnelsAPI[$personneKey]['equipes']
                    ];
                    $this->personneModel->updatePersonne($personnelsAPI[$personneKey]['id_personne'], $data);
                }
            }

            foreach ($personnelsAPI as $personne) {
                $data = [
                    'id_personne' => $personne['id_personne'],
                    'nom' => mb_strtoupper($personne['nom_usage']),
                    'prenom' => $personne['prenom'],
                    'statut' => $personne['statut'],
                    'equipe' => $personne['equipes']
                ];
                if (!$this->personneModel->getPersonne($personne['id_personne'])) {
                    $this->personneModel->insertPersonne($data);
                }
            }
        }
    }

    /**
     * Fonction de mise à jour de toutes les responsabilités en base de données
     * @param $responsabilitesArray
     * @return void
     */
    public function updateResponsabiliteDB($responsabilitesArray)
    {
        if (empty($responsabilitesArray)) {
            $this->responsabiliteModel->deleteAll();
        } else {
            $result = $this->responsabiliteModel->getAllResponsabilites();

            foreach ($result as $responsabiliteBDD) {
                $responsabiliteKey = array_search($responsabiliteBDD->id_responsabilite,
                    array_column($responsabilitesArray, 'id_personne_resp'));
                if ($responsabiliteKey === false) {
                    $this->responsabiliteModel->deleteResponsabilite($responsabiliteBDD->id_responsabilite);
                } else {
                    $data = [
                        'libelle' => $responsabilitesArray[$responsabiliteKey]['responsabilite']['responsabilite'],
                        'id_personne' => $responsabilitesArray[$responsabiliteKey]['personne']['id_personne']
                    ];
                    $this->responsabiliteModel->updateResponsabilite($responsabiliteBDD->id_responsabilite, $data);
                }
            }

            foreach ($responsabilitesArray as $responsabilite) {
                $insert = [
                    'id_responsabilite' => $responsabilite['id_personne_resp'],
                    'libelle' => $responsabilite['responsabilite']['responsabilite'],
                    'id_personne' => $responsabilite['personne']['id_personne']
                ];
                if (!$this->responsabiliteModel->getResponsabilite($responsabilite['id_personne_resp'])) {
                    $this->responsabiliteModel->insertResponsabilite($insert);
                }
            }
        }
    }

    /**
     * Fonction de mise à jour de tous les mails en base de données
     * @param $mailsArray
     * @return void
     */
    public function updateMailDB($mailsArray)
    {
        //TODO : a faire next  ..
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
        if (empty($localisationsAPI)) {
            $update = [
                'telephone' => NULL,
                'numero_bureau' => NULL
            ];
            $this->personneModel->updateAll($update);
        } else {
            foreach ($localisationsAPI as $localisation) {
                $update = [
                    'telephone' => $localisation['tel_professionnel'],
                    'numero_bureau' => $localisation['numero_bureau']
                ];

                $this->personneModel->updatePersonne($localisation['id_personne'], $update);
            }
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
             * TRIGGER sur employeur_sejour mis en place lors du delete d'un séjour
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

    /**
     * Fonction de mise à jour de tous les statuts en base de données
     * @param $statusAPI
     * @return void
     */
    public function updateStatusDB($statusAPI)
    {
        $db = db_connect();
        $builder = $db->table('statut');

        if (empty($statusAPI)) {
            $builder->delete();
        } else {
            $result = $builder->select()
                ->get()
                ->getResultArray();

            foreach ($result as $statutDB) {
                $statutKey = array_search($statutDB['id_statut'],
                    array_column($statusAPI, 'id_statut'));
                if ($statutKey === false) {
                    $builder->where('id_statut', $statutDB['id_statut'])
                        ->delete();
                } else {
                    $data = [
                        'id_statut' => $statusAPI[$statutKey]['id_statut'],
                        'statut' => $statusAPI[$statutKey]['statut']
                    ];
                    $builder->set($data);
                    $builder->where('id_statut', $statutDB['id_statut'])
                        ->update();
                }
            }

            foreach ($statusAPI as $statut) {
                $builder = $db->table('statut');
                $insert = [
                    'id_statut' => $statut['id_statut'],
                    'statut' => $statut['statut']
                ];

                $query = $builder->select()
                    ->where('id_statut', $statut['id_statut'])
                    ->get();

                $builder->set($insert)->where('id_statut', $statut['id_statut']);

                if ($query->getNumRows() === 0) {
                    $builder->insert();
                }
            }
            $db->close();
        }
    }

    /**
     * Fonction de mise à jour de toutes les équipes en base de données
     * @param $equipesAPI
     * @return void
     */
    public function updateEquipeDB($equipesAPI)
    {
        $db = db_connect();
        $builder = $db->table('equipe');

        if (empty($equipesAPI)) {
            $builder->delete();
        } else {
            $result = $builder->select()
                ->get()
                ->getResultArray();

            foreach ($result as $equpeDB) {
                $equipeKey = array_search($equpeDB['id_equipe'],
                    array_column($equipesAPI, 'id_equipe'));
                if ($equipeKey === false) {
                    $builder->where('id_equipe', $equpeDB['id_equipe'])
                        ->delete();
                } else {
                    $data = [
                        'id_equipe' => $equipesAPI[$equipeKey]['id_groupe'],
                        'nom_court_groupe' => $equipesAPI[$equipeKey]['nom_court_groupe'],
                        'nom_long_groupe' => $equipesAPI[$equipeKey]['nom_long_groupe']
                    ];
                    $builder->set($data);
                    $builder->where('id_equipe', $equpeDB['id_equipe'])
                        ->update();
                }
            }

            foreach ($equipesAPI as $equipe) {
                $builder = $db->table('equipe');
                $insert = [
                    'id_equipe' => $equipe['id_groupe'],
                    'nom_court_groupe' => $equipe['nom_court_groupe'],
                    'nom_long_groupe' => $equipe['nom_long_groupe']
                ];

                $query = $builder->select()
                    ->where('id_equipe', $equipe['id_groupe'])
                    ->get();

                $builder->set($insert)->where('id_equipe', $equipe['id_groupe']);

                if ($query->getNumRows() === 0) {
                    $builder->insert();
                }
            }
            $db->close();
        }
    }

    /**
     * Fonction de création des photos de profiles des personnes
     * @param $profilePictures
     * @return void
     */
    public function createProfilePictures($profilePictures)
    {
        if (empty($profilePictures)) {
            //TODO : A faire
        } else {
            foreach ($profilePictures as $profilePicture) {
                if (isset($profilePicture['photo'])) {
                    file_put_contents('assets/images/profile/' . $profilePicture['id_personne'] . '.jpg',
                        file_get_contents($profilePicture['photo']));
                } else {
                    file_put_contents('assets/images/profile/' . $profilePicture['id_personne'] . '.jpg',
                        file_get_contents('assets/images/default_profile.jpg'));
                }
            }
        }
    }

    /**
     * Fonction qui permet de retourner les personnes en base de données
     * @return array
     */
    public function getPersonnes(): array
    {
        return $this->personneModel->getAllPersonnes();
    }

    /**
     * Fonction qui permet la recherche
     * @param $query
     * @param $statuts
     * @param $equipes
     * @param $tuteurs
     * @return array
     */
    public function search($query, $statuts, $equipes, $tuteurs): array
    {
        $db = db_connect();
        $builder = $db->table('personne');
        $builder->select();

        // Filtre des noms et des prénoms
        if (!empty($query)) {
            $queryString = explode(" ", $query);
            foreach ($queryString as $char) {
                $builder->like('nom', $char)
                    ->orLike('prenom', $char);
            }
        }

        // Filtre des status
        if (!empty($statuts)) {
            foreach ($statuts as $statut) {
                $builder->orlike('statut', $statut);
            }
        }

        // Filtre des équipes
        if (!empty($equipes)) {
            foreach ($equipes as $equipe) {
                $builder->orlike('equipe', $equipe);
            }
        }

        // Filtre des tuteurs
        if (!empty($tuteurs)) {
            foreach ($tuteurs as $tuteur) {
                $fullName = explode(" ", $tuteur);
                $prenom = $fullName[0];
                $nom = $fullName[1];
                $builder->orWhere("id_personne IN 
                (SELECT s.id_personne 
                FROM sejour s, encadrant e, personne p 
                WHERE s.id_sejour=e.id_sejour 
                AND e.id_personne=p.id_personne 
                AND p.prenom=" . $db->escape($prenom) . " 
                AND p.nom=" . $db->escape($nom) . ")");
            }
        }

        $result = $builder->orderBy('nom')
            ->get()
            ->getResultArray();
        $db->close();
        return $result;
    }

    /**
     * Fonction qui permet de retourner les statuts en base de données
     * @return array
     */
    public function getStatuts(): array
    {
        $db = db_connect();
        $builder = $db->table('statut');
        $result = $builder->select()
            ->orderBy('statut')
            ->get()
            ->getResultArray();
        $db->close();
        return $result;
    }

    /**
     * Fonction qui permet de retourner les équipes en base de données
     * @return array
     */
    public function getEquipes(): array
    {
        $db = db_connect();
        $builder = $db->table('equipe');
        $result = $builder->select()
            ->orderBy('nom_court_groupe')
            ->get()
            ->getResultArray();
        $db->close();
        return $result;
    }

    /**
     * Fonction qui permet de retourner les tuteurs en base de données
     * @return array
     */
    public function getEncadrants(): array
    {
        $db = db_connect();
        $builder = $db->table('personne');
        $result = $builder->select()
            ->where('id_personne IN (SELECT id_personne FROM encadrant)')
            ->orderBy('nom')
            ->get()
            ->getResultArray();
        $db->close();
        return $result;
    }

    public function beautifulPrint($data)
    {
        print("<pre>" . print_r($data, true) . "</pre>");
    }
}
