<?php

namespace App\Controllers;

use App\Models\APIModel;
use App\Models\BureauModel;
use App\Models\EmployeurModel;
use App\Models\EncadrantModel;
use App\Models\EquipeModel;
use App\Models\FinancementModel;
use App\Models\LocalisationModel;
use App\Models\MailModel;
use App\Models\PersonneModel;
use App\Models\RattachementModel;
use App\Models\ResponsabiliteModel;
use App\Models\SejourModel;
use App\Models\StatutModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;
use CodeIgniter\Session\Session;
use Config\Services;
use DateInterval;
use Exception;
use Psr\Log\LoggerInterface;

class Home extends BaseController
{
    protected array $personnels;
    protected array $allPersonnels;
    protected APIModel $ApiModel;
    protected PersonneModel $personneModel;
    protected ResponsabiliteModel $responsabiliteModel;
    protected MailModel $mailModel;
    protected EmployeurModel $employeurModel;
    protected SejourModel $sejourModel;
    protected FinancementModel $financementModel;
    protected EncadrantModel $encadrantModel;
    protected StatutModel $statutModel;
    protected EquipeModel $equipeModel;
    protected BureauModel $bureauModel;
    protected RattachementModel $rattachementModel;
    protected LocalisationModel $localisationModel;
    protected Session $session;


    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->ApiModel = new APIModel();
        $this->personneModel = new PersonneModel();
        $this->responsabiliteModel = new ResponsabiliteModel();
        $this->mailModel = new MailModel();
        $this->employeurModel = new EmployeurModel();
        $this->sejourModel = new SejourModel();
        $this->financementModel = new FinancementModel();
        $this->encadrantModel = new EncadrantModel();
        $this->statutModel = new StatutModel();
        $this->equipeModel = new EquipeModel();
        $this->bureauModel = new BureauModel();
        $this->rattachementModel = new RattachementModel();
        $this->localisationModel = new LocalisationModel();
        $this->session = Services::session();
    }

    public function index(): string
    {
        // Récupération des informations de l'utilisateur connecté depuis la session
        $user = $this->session->get('user');
        if ($user) {
            // Récupération des informations détaillées de la personne connectée à partir de son login
            $personneConnectee = $this->personneModel->getPersonneLogin($user['login']);
            if ($personneConnectee) {
                // Stockage des informations de la personne connectée dans les données à envoyer à la vue
                $data['personneConnectee'] = $personneConnectee;
            }
        }

        // Récupération de la liste complète de tous les personnels
        $this->allPersonnels = $this->getPersonnes();

        // Vérification si des filtres de recherche sont appliqués dans la requête GET
        if ($this->request->getGet('q')
            || $this->request->getGet('statut[]')
            || $this->request->getGet('equipe[]')
            || $this->request->getGet('tuteur[]')) {

            // Récupération des filtres de recherche depuis la requête GET
            $query = $this->request->getGet('q');
            $data['query'] = $query;
            $statut = $this->request->getGet('statut[]');
            $data['filtreStatut'] = $statut;
            $equipe = $this->request->getGet('equipe[]');
            $data['filtreEquipe'] = $equipe;
            $tuteur = $this->request->getGet('tuteur[]');
            $data['filtreTuteur'] = $tuteur;

            // Effectuer la recherche en fonction des filtres appliqués
            $this->personnels = $this->search($query, $statut, $equipe, $tuteur);
        } else {
            // Si aucun filtre n'est appliqué, afficher la liste complète des personnels
            $this->personnels = $this->allPersonnels;
        }

        // Récupération des listes de statuts, équipes et tuteurs pour les filtres
        $data['statut'] = $this->getStatuts();
        $data['equipe'] = $this->getEquipes();
        $data['tuteur'] = $this->getEncadrants();

        // Stockage des personnels filtrés et de tous les personnels dans les données à envoyer à la vue
        $data['personnes'] = $this->personnels;
        $data['allPersonnels'] = $this->allPersonnels;

        return view('frontoffice/home', $data);
    }


    /**
     * Fonction qui permet de retourner les personnes en base de données
     * @return array
     */
    public function getPersonnes(): array
    {
        return $this->personneModel->getAllPersonnes('nom');
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
        return $this->personneModel->searchPersonne($query, $statuts, $equipes, $tuteurs);
    }

    /**
     * Fonction qui permet de retourner les statuts en base de données où il y a des personnes dedans
     * @return array
     */
    public function getStatuts(): array
    {
        return $this->statutModel->getStatutsNonVide();
    }

    /**
     * Fonction qui permet de retourner les équipes utiles en base de données
     * @return array
     */
    public function getEquipes(): array
    {
        return $this->equipeModel->getEquipesFiltre();
    }

    /**
     * Fonction qui permet de retourner les tuteurs en base de données
     * @return array
     */
    public function getEncadrants(): array
    {
        return $this->personneModel->getAllEncadrants();
    }

    /**
     * Fonction de mise à jour de la base de données TODO : A automatiser !
     * @return void
     * @throws Exception
     */
    public function updateDB()
    {
        $insertImages = [];
        $insertLogin = [];

        // Données brutes indépendantes des personnes
        $allEmployeurs = $this->getAllDataFromURL('org_payeurs');
        $allStatuts = $this->getAllDataFromURL('statuts');
        $allEquipes = $this->getAllDataFromURL('groupes');
        $allBureaux = $this->getAllDataFromURL('bureaux');

        // Données en fonction des personnes
        $allPersonnels = $this->getAllDataFromURL('personnels');
        $allSejours = $this->getAllDataFromURL('sejours');
        $allEncadrants = $this->getAllDataFromURL('encadrants');
        $allLocalisations = $this->getAllDataFromURL('localisation_personnels');
        $allMails = $this->getAllDataFromURL('mails_pro');
        $allResponsabilites = $this->getAllDataFromURL('personne_responsabilites');
        $allPersonnes = $this->getAllDataFromURL('personnes');
        $allFinancements = $this->getAllDataFromURL('financements');
        $allRattachements = $this->getAllDataFromURL('rattachements');

        // On vérifie si on a bien trouvé des données depuis l’API, si oui, on fait les appels pour faire les mises à jour.
        if (isset($allBureaux)) {
            $this->updateBureauDB($allBureaux);
        }

        if (isset($allStatuts)) {
            $this->updateStatusDB($allStatuts);
        }

        if (isset($allEmployeurs)) {
            $this->updateEmployeurDB($allEmployeurs);
        }

        if (isset($allEquipes)) {
            $this->updateEquipeDB($allEquipes);
        }

        if (isset($allPersonnels)) {
            $temp = [];
            // On parcourt les personnels pour récupérer uniquement les personnels où le dernier séjour date de moins de 3 mois.
            foreach ($allPersonnels as $personnel) {
                if ($personnel['id_sejour'] != null) {
                    // Création d’un intervalle de 3 mois
                    $interval = new DateInterval('P3M');

                    // Création de la date à partir de l’attribut date_fin_sejour de personnel
                    // avec l’ajout de l’intervalle et du changement de format
                    $date_fin = date_create_immutable(Time::createFromFormat("d/m/Y", $personnel['date_fin_sejour']))
                        ->add($interval)->format('Y-m-d');

                    // Récupération de la date actuelle avec le même format
                    $date_actuelle = date('Y-m-d');

                    // Si la date de fin (+3mois) est plus grande que la date actuelle, alors on ajoute dans la table temporaire.
                    if ($date_fin > $date_actuelle) {
                        $temp[] = $personnel;
                    }
                }
            }
            $allPersonnels = $temp;
            $this->updatePersonnelDB($allPersonnels);
        }

        if (isset($allLocalisations)) {
            $this->updateLocalisationDB($allLocalisations);
        }

        if (isset($allResponsabilites)) {
            $this->updateResponsabiliteDB($allResponsabilites);
        }

        if (isset($allMails)) {
            $this->updateMailDB($allMails);
        }

        if (isset($allSejours)) {
            $tempSejour = [];
            // On parcourt les personnels pour récupérer uniquement les personnels où le dernier séjour date de moins de 3 mois.
            foreach ($allSejours as $sejour) {
                // Création d’un intervalle de 3 mois
                $interval = new DateInterval('P3M');

                // Création de la date à partir de l’attribut date_fin_sejour de personnel
                // avec l’ajout de l’intervalle et du changement de format
                $date_fin = date_create_immutable(Time::createFromFormat("d/m/Y", $sejour['date_fin_sejour']))
                    ->add($interval)->format('Y-m-d');

                // Récupération de la date actuelle avec le même format
                $date_actuelle = date('Y-m-d');

                // Si la date de fin (+3mois) est plus grande que la date actuelle, alors on ajoute dans la table temporaire.
                if ($date_fin > $date_actuelle) {
                    $tempSejour[] = $sejour;
                }
            }
            $allSejours = $tempSejour;
            $this->updateSejourBD($allSejours);
        }

        if (isset($allFinancements)) {
            $this->updateFinancementDB($allFinancements);
        }

        if (isset($allRattachements)) {
            $this->updateRattachementDB($allRattachements);
        }

        if (isset($allPersonnes)) {
            // On parcourt les personnes et pour chaque personne, on récupère sa photo et son login et on récupère le statut du personnel
            foreach ($allPersonnes as $personne) {
                $personnelKey = array_search($personne['id_personne'],
                    array_column($allPersonnels, 'id_personne'));
                if ($personnelKey !== false) {
                    $insertImages[] = ['id_personne' => $personne['id_personne'],
                        'photo' => $personne['photo'],
                        'statut' => $allPersonnels[$personnelKey]['statut']];
                    $insertLogin[] = ['id_personne' => $personne['id_personne'],
                        'login' => $personne['info_prof']['login_unite']];
                }
            }
            $this->createProfilePictures($insertImages);
            $this->updateLoginDB($insertLogin);
        }

        if (isset($allEncadrants)) {
            $this->updateEncadrantDB($allEncadrants);
        }
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
     * Fonction qui met à jour tous les bureaux en base de données depuis l'API
     * @param $bureauAPI
     * @return void
     */
    public function updateBureauDB($bureauAPI)
    {
        // Si l'API ne renvoie aucun bureau, supprimer tous les bureaux de la base de données
        if (empty($bureauAPI)) {
            $this->bureauModel->deleteAll();
        } else {
            // Récupérer tous les bureaux existants dans la base de données
            $result = $this->bureauModel->getAllBureaux();

            // Parcourir tous les bureaux de la base de données
            foreach ($result as $bureauBDD) {
                // Rechercher si le bureau de la base de données existe dans les données de l'API
                $bureauKey = array_search($bureauBDD->id_bureau, array_column($bureauAPI, 'id_bureau'));

                // Si le bureau n'existe pas dans l'API, le supprimer de la base de données
                if ($bureauKey === false) {
                    $this->bureauModel->deleteBureau($bureauBDD->id_mail);
                } else {
                    // Si le bureau existe dans l'API, mettre à jour son numéro dans la base de données
                    $data = [
                        'numero' => $bureauAPI[$bureauKey]['numero_bureau']
                    ];
                    $this->bureauModel->updateBureau($bureauBDD->id_bureau, $data);
                }
            }

            // Parcourir tous les bureaux de l'API
            foreach ($bureauAPI as $bureau) {
                $id_bureau = $bureau['id_bureau'];
                $insert = [
                    'id_bureau' => $id_bureau,
                    'numero' => $bureau['numero_bureau']
                ];

                // Si le bureau n'existe pas dans la base de données, l'insérer
                if (!$this->bureauModel->getBureau($id_bureau)) {
                    $this->bureauModel->insertBureau($insert);
                }
            }
        }
    }

    /**
     * Fonction de mise à jour de tous les statuts en base de données
     * @param $statusAPI
     * @return void
     */
    public function updateStatusDB($statusAPI)
    {
        // Si l'API ne renvoie aucun statut, supprimer tous les statuts de la base de données
        if (empty($statusAPI)) {
            $this->statutModel->deleteAll();
        } else {
            // Récupérer tous les statuts existants dans la base de données
            $result = $this->statutModel->getAllStatuts();

            // Parcourir tous les statuts de la base de données
            foreach ($result as $statutDB) {
                // Rechercher si le statut de la base de données existe dans les données de l'API
                $statutKey = array_search($statutDB->id_statut, array_column($statusAPI, 'id_statut'));

                // Si le statut n'existe pas dans l'API, le supprimer de la base de données
                if ($statutKey === false) {
                    $this->statutModel->deleteStatut($statutDB->id_statut);
                } else {
                    // Si le statut existe dans l'API, mettre à jour ses informations dans la base de données
                    $update = [
                        'statut' => $statusAPI[$statutKey]['statut']
                    ];
                    $this->statutModel->updateStatut($statutDB->id_statut, $update);
                }
            }

            // Parcourir tous les statuts de l'API
            foreach ($statusAPI as $statut) {
                $id_statut = $statut['id_statut'];
                $insert = [
                    'id_statut' => $id_statut,
                    'statut' => $statut['statut']
                ];

                // Si le statut n'existe pas dans la base de données, l'insérer
                if (!$this->statutModel->getStatut($id_statut)) {
                    $this->statutModel->insertStatut($insert);
                }
            }
        }
    }


    /**
     * Fonction de mise à jour de tous les employeurs en base de données
     * @param $employeursArray
     * @return void
     */
    public function updateEmployeurDB($employeursArray)
    {
        // Si l'API ne renvoie aucun employeur, supprimer tous les employeurs de la base de données
        if (empty($employeursArray)) {
            $this->employeurModel->deleteAll();
        } else {
            // Récupérer tous les employeurs existants dans la base de données
            $result = $this->employeurModel->getAllEmployeurs();

            // Parcourir tous les employeurs de la base de données
            foreach ($result as $employeurBDD) {
                // Rechercher si l'employeur de la base de données existe dans les données de l'API
                $employeurKey = array_search($employeurBDD->id_employeur, array_column($employeursArray, 'id_org_payeur'));

                // Si l'employeur n'existe pas dans l'API, le supprimer de la base de données
                if ($employeurKey === false) {
                    $this->employeurModel->deleteEmployeur($employeurBDD->id_employeur);
                } else {
                    // Si l'employeur existe dans l'API, mettre à jour ses informations dans la base de données
                    $update = [
                        'nom' => $employeursArray[$employeurKey]['organisme_payeur'],
                        'nom_court' => $employeursArray[$employeurKey]['nom_court_op']
                    ];
                    $this->employeurModel->updateEmployeur($employeurBDD->id_employeur, $update);
                }
            }

            // Parcourir tous les employeurs de l'API
            foreach ($employeursArray as $employeur) {
                $id_employeur = $employeur['id_org_payeur'];
                $insert = [
                    'id_employeur' => $id_employeur,
                    'nom' => $employeur['organisme_payeur'],
                    'nom_court' => $employeur['nom_court_op']
                ];

                // Si l'employeur n'existe pas dans la base de données, l'insérer
                if (!$this->employeurModel->getEmployeur($id_employeur)) {
                    $this->employeurModel->insertEmployeur($insert);
                }
            }
        }
    }


    /**
     * Fonction de mise à jour de toutes les équipes en base de données
     * @param $equipesAPI
     * @return void
     * @throws Exception
     */
    public function updateEquipeDB($equipesAPI)
    {
        // Si l'API ne renvoie aucune équipe, supprimer toutes les équipes de la base de données
        if (empty($equipesAPI)) {
            $this->equipeModel->deleteAll();
        } else {
            // Récupérer toutes les équipes existantes dans la base de données
            $result = $this->equipeModel->getAllEquipes();
            $date_actuelle = date('Y-m-d');

            // Parcourir toutes les équipes de la base de données
            foreach ($result as $equipeDB) {
                // Rechercher si l'équipe de la base de données existe dans les données de l'API
                $equipeKey = array_search($equipeDB->id_equipe, array_column($equipesAPI, 'id_groupe'));

                // Si l'équipe n'existe pas dans l'API, la supprimer de la base de données
                if ($equipeKey === false) {
                    $this->equipeModel->deleteEquipe($equipeDB->id_equipe);
                } else {
                    if (isset($equipesAPI[$equipeKey]['date_fin_groupe'])) {
                        $interval = new DateInterval('P3M');
                        $date_fin = date_create_immutable(Time::createFromFormat("d/m/Y", $equipesAPI[$equipeKey]['date_fin_groupe']))
                            ->add($interval)->format('Y-m-d');

                        // Si la date de fin de groupe + 3 mois est inférieure à la date actuelle, supprimer l'équipe
                        if ($date_fin < $date_actuelle) {
                            $this->equipeModel->deleteEquipe($equipeDB->id_equipe);
                        } else {
                            // Si l'équipe existe dans l'API, mettre à jour ses informations dans la base de données
                            $update = [
                                'nom_court' => $equipesAPI[$equipeKey]['nom_court_groupe'],
                                'nom_long' => $equipesAPI[$equipeKey]['nom_long_groupe']
                            ];
                            $this->equipeModel->updateEquipe($equipeDB->id_equipe, $update);
                        }
                    } else {
                        // Si l'équipe existe dans l'API, mettre à jour ses informations dans la base de données
                        $update = [
                            'nom_court' => $equipesAPI[$equipeKey]['nom_court_groupe'],
                            'nom_long' => $equipesAPI[$equipeKey]['nom_long_groupe']
                        ];
                        $this->equipeModel->updateEquipe($equipeDB->id_equipe, $update);
                    }
                }
            }

            // Parcourir toutes les équipes de l'API
            foreach ($equipesAPI as $equipe) {
                $id_equipe = $equipe['id_groupe'];
                $interval = new DateInterval('P3M');
                $date_fin = date_create_immutable(Time::createFromFormat("d/m/Y", $equipe['date_fin_groupe']))
                    ->add($interval)->format('Y-m-d');

                // Vérifier si l'équipe est active dans l'intervalle de 3 mois après la date de fin
                if ($date_fin >= $date_actuelle) {
                    $insert = [
                        'id_equipe' => $id_equipe,
                        'nom_court' => $equipe['nom_court_groupe'],
                        'nom_long' => $equipe['nom_long_groupe']
                    ];

                    // Si l'équipe n'existe pas dans la base de données, l'insérer
                    if (!$this->equipeModel->getEquipe($id_equipe)) {
                        $this->equipeModel->insertEquipe($insert);
                    }
                }
            }
        }
    }


    /**
     * Fonction de mise à jour de toutes les personnes en base de données
     * @param $personnelsAPI
     * @return void
     * @throws Exception
     */
    public function updatePersonnelDB($personnelsAPI)
    {
        // Si l'API ne renvoie aucun personnel, supprimer tous les personnels de la base de données
        if (empty($personnelsAPI)) {
            /**
             * Contrainte de clé étrangère CASCADE mise sur PERSONNE
             */
            $this->personneModel->deleteAll();
        } else {
            // Récupérer toutes les personnes existantes dans la base de données
            $personnesBD = $this->personneModel->getAllPersonnes('nom');

            // Parcourir toutes les personnes de la base de données
            foreach ($personnesBD as $personne) {
                // Rechercher si la personne de la base de données existe dans les données de l'API
                $personneKey = array_search($personne->id_personne, array_column($personnelsAPI, 'id_personne'));

                // Si la personne n'existe pas dans l'API, la supprimer de la base de données
                if ($personneKey === false) {
                    $this->personneModel->deletePersonne($personne->id_personne);
                } else {
                    // Calculer la date de fin de séjour augmentée de 3 mois
                    $interval = new DateInterval('P3M');
                    $date_fin = date_create_immutable(Time::createFromFormat("d/m/Y", $personnelsAPI[$personneKey]['date_fin_sejour']))
                        ->add($interval)->format('Y-m-d');
                    $date_actuelle = date('Y-m-d');

                    // Si la date de fin de séjour + 3 mois est inférieure à la date actuelle, supprimer la personne
                    if ($date_fin < $date_actuelle) {
                        $this->personneModel->deletePersonne($personne->id_personne);
                    } else {
                        // Sinon, mettre à jour les informations de la personne
                        $data = [
                            'nom' => mb_strtoupper($personnelsAPI[$personneKey]['nom_usage']),
                            'prenom' => $personnelsAPI[$personneKey]['prenom'],
                        ];
                        $this->personneModel->updatePersonne($personnelsAPI[$personneKey]['id_personne'], $data);
                    }
                }
            }

            // Parcourir tous les personnels de l'API
            foreach ($personnelsAPI as $personne) {
                // Calculer la date de fin de séjour augmentée de 3 mois
                $interval = new DateInterval('P3M');
                $date_fin = date_create_immutable(Time::createFromFormat("d/m/Y", $personne['date_fin_sejour']))
                    ->add($interval)->format('Y-m-d');
                $date_actuelle = date('Y-m-d');

                $id_personne = $personne['id_personne'];

                $data = [
                    'id_personne' => $id_personne,
                    'role' => 'normal',
                    'nom' => mb_strtoupper($personne['nom_usage']),
                    'prenom' => $personne['prenom'],
                ];

                // Vérification si la personne n’existe pas encore et si sa date de fin de séjour n’a pas dépassé 3 mois la date actuelle
                if (!$this->personneModel->getPersonne($id_personne) && $date_fin > $date_actuelle) {
                    $this->personneModel->insertPersonne($data);

                }
            }
        }
    }

    /**
     * Fonction de mse à jour de toutes les localisations en base de données
     * @param $localisationsAPI
     * @return void
     * @throws Exception
     */
    public function updateLocalisationDB($localisationsAPI)
    {
        // Si l'API ne renvoie aucune localisation, supprimer toutes les localisations de la base de données
        if (empty($localisationsAPI)) {
            $this->localisationModel->deleteAll();
        } else {
            // Récupérer toutes les localisations existantes dans la base de données
            $result = $this->localisationModel->getAllLocalisations();

            // Parcourir toutes les localisations de la base de données
            foreach ($result as $localisationBDD) {
                // Rechercher si la localisation de la base de données existe dans les données de l'API
                $localisationKey = array_search($localisationBDD->id_localisation, array_column($localisationsAPI, 'id_localisation'));

                // Si la localisation n'existe pas dans l'API, la supprimer de la base de données
                if ($localisationKey === false) {
                    $this->localisationModel->deleteLocalisation($localisationBDD->id_localisation);
                } else {
                    // Vérifier la date de fin de séjour + 3 mois
                    $date_fin = date_create_immutable(Time::createFromFormat("d/m/Y", $localisationsAPI[$localisationKey]['date_fin_sejour']))
                        ->add(new DateInterval('P3M'))->format('Y-m-d');
                    $date_actuelle = date('Y-m-d');

                    // Si la date de fin de séjour + 3 mois est inférieure à la date actuelle, supprimer la localisation
                    if ($date_fin < $date_actuelle) {
                        $this->localisationModel->deleteLocalisation($localisationBDD->id_localisation);
                    } else {
                        // Si la localisation existe dans l'API, mettre à jour ses informations dans la base de données
                        $update = [
                            'telephone' => $localisationsAPI[$localisationKey]['tel_professionnel'],
                            'bureau' => $localisationsAPI[$localisationKey]['id_bureau'],
                            'sejour' => $localisationsAPI[$localisationKey]['id_sejour'],
                            'personne' => $localisationsAPI[$localisationKey]['id_personne']
                        ];
                        $this->localisationModel->updateLocalisation($localisationBDD->id_localisation, $update);
                    }
                }
            }

            // Parcourir toutes les localisations de l'API
            foreach ($localisationsAPI as $localisation) {
                // Vérifier la date de fin de séjour + 3 mois
                $date_fin = date_create_immutable(Time::createFromFormat("d/m/Y", $localisation['date_fin_sejour']))
                    ->add(new DateInterval('P3M'))->format('Y-m-d');
                $date_actuelle = date('Y-m-d');

                $id_localisation = $localisation['id_localisation'];
                $insert = [
                    'id_localisation' => $id_localisation,
                    'telephone' => $localisation['tel_professionnel'],
                    'bureau' => $localisation['id_bureau'],
                    'sejour' => $localisation['id_sejour'],
                    'personne' => $localisation['id_personne']
                ];

                // Si la localisation n'existe pas dans la base de données et
                // si la date de fin de séjour + 3 mois est inférieure à la date actuelle, l'insérer
                if (!$this->localisationModel->getLocalisation($id_localisation) && $date_fin > $date_actuelle) {
                    $this->localisationModel->insertLocalisation($insert);
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
        // Si le tableau de responsabilités de l'API est vide, supprimer toutes les responsabilités de la base de données
        if (empty($responsabilitesArray)) {
            $this->responsabiliteModel->deleteAll();
        } else {
            // Récupérer toutes les responsabilités existantes dans la base de données
            $result = $this->responsabiliteModel->getAllResponsabilites();

            // Parcourir chaque responsabilité existante dans la base de données
            foreach ($result as $responsabiliteBDD) {
                // Rechercher l'ID de la responsabilité dans le tableau de l'API
                $responsabiliteKey = array_search($responsabiliteBDD->id_responsabilite,
                    array_column($responsabilitesArray, 'id_personne_resp'));

                // Si la responsabilité n'existe pas dans l'API, la supprimer de la base de données
                if ($responsabiliteKey === false) {
                    $this->responsabiliteModel->deleteResponsabilite($responsabiliteBDD->id_responsabilite);
                } else {
                    // Sinon, préparer les données pour la mise à jour
                    $data = [
                        'libelle' => $responsabilitesArray[$responsabiliteKey]['responsabilite']['responsabilite'],
                        'id_personne' => $responsabilitesArray[$responsabiliteKey]['personne']['id_personne']
                    ];
                    // Mettre à jour la responsabilité dans la base de données
                    $this->responsabiliteModel->updateResponsabilite($responsabiliteBDD->id_responsabilite, $data);
                }
            }

            // Parcourir chaque responsabilité dans le tableau de l'API
            foreach ($responsabilitesArray as $responsabilite) {
                $id_responsabilite = $responsabilite['id_personne_resp'];
                $id_personne = $responsabilite['personne']['id_personne'];
                // Préparer les données pour l'insertion
                $insert = [
                    'id_responsabilite' => $id_responsabilite,
                    'libelle' => $responsabilite['responsabilite']['responsabilite'],
                    'id_personne' => $id_personne
                ];
                // Si la responsabilité n'existe pas encore dans la base de données et que la personne associée existe,
                // insérer la nouvelle responsabilité dans la base de données
                if (!$this->responsabiliteModel->getResponsabilite($id_responsabilite)
                    && $this->personneModel->getPersonne($id_personne)) {
                    $this->responsabiliteModel->insertResponsabilite($insert);
                }
            }
        }
    }

    /**
     * Fonction de mise à jour de tous les mails en base de données
     * @param $mailsAPI
     * @return void
     */
    public function updateMailDB($mailsAPI)
    {
        // Si le tableau des mails de l'API est vide, supprimer tous les mails de la base de données
        if (empty($mailsAPI)) {
            $this->mailModel->deleteAll();
        } else {
            // Récupérer tous les mails existants dans la base de données
            $result = $this->mailModel->getAllMails();

            // Parcourir chaque mail existant dans la base de données
            foreach ($result as $mailBDD) {
                // Rechercher l'ID du mail dans le tableau des mails de l'API
                $mailKey = array_search($mailBDD->id_mail, array_column($mailsAPI, 'id_mail'));

                // Si le mail n'existe pas dans l'API, le supprimer de la base de données
                if ($mailKey === false) {
                    $this->mailModel->deleteMail($mailBDD->id_mail);
                } else {
                    // Sinon, préparer les données pour la mise à jour
                    $data = [
                        'libelle' => $mailsAPI[$mailKey]['mail'],
                        'type' => $mailsAPI[$mailKey]['type_mail']['type_mail'],
                        'id_personne' => $mailsAPI[$mailKey]['personne']['id_personne']
                    ];
                    // Mettre à jour le mail dans la base de données
                    $this->mailModel->updateMail($mailBDD->id_mail, $data);
                }
            }

            // Parcourir chaque mail dans le tableau des mails de l'API
            foreach ($mailsAPI as $mail) {
                // Ignorer les mails de type 'Perso'
                if ($mail['type_mail']['type_mail'] != 'Perso') {
                    $id_mail = $mail['id_mail'];
                    $id_personne = $mail['personne']['id_personne'];
                    // Préparer les données pour l'insertion
                    $insert = [
                        'id_mail' => $id_mail,
                        'libelle' => $mail['mail'],
                        'type' => $mail['type_mail']['type_mail'],
                        'id_personne' => $id_personne
                    ];

                    // Si le mail n'existe pas encore dans la base de données et que la personne associée existe,
                    // insérer le nouveau mail dans la base de données
                    if (!$this->mailModel->getMail($id_mail)
                        && $this->personneModel->getPersonne($id_personne)) {
                        $this->mailModel->insertMail($insert);
                    }
                }
            }
        }
    }

    /**
     * Fonction de mise à jour de tous les séjours en base de données
     * @param $sejourAPI
     * @return void
     * @throws Exception
     */
    public function updateSejourBD($sejourAPI)
    {
        // Si le tableau des séjours de l'API est vide, supprimer tous les séjours de la base de données
        if (empty($sejourAPI)) {
            /**
             * TRIGGER sur employeur_sejour mis en place lors du delete d’un séjour
             */
            $this->sejourModel->deleteAll();
        } else {
            // Récupérer tous les séjours existants dans la base de données
            $result = $this->sejourModel->getAllSejours();

            // Parcourir chaque séjour existant dans la base de données
            foreach ($result as $sejourBDD) {
                // Rechercher l'ID du séjour dans le tableau des séjours de l'API
                $sejourKey = array_search($sejourBDD->id_sejour, array_column($sejourAPI, 'id_sejour'));

                // Si le séjour n'existe pas dans l'API, le supprimer de la base de données
                if ($sejourKey === false) {
                    $this->sejourModel->deleteSejour($sejourBDD->id_sejour);
                } // Si la personne associée au séjour n'existe pas dans la base de données, supprimer également le séjour
                else if ($this->personneModel->getPersonne($sejourBDD->id_personne) === null) {
                    $this->sejourModel->deleteSejour($sejourBDD->id_sejour);
                } else {
                    // Sinon, préparer les données pour la mise à jour
                    $update = [
                        'date_debut' => Time::createFromFormat("d/m/Y", $sejourAPI[$sejourKey]['date_debut_sejour']),
                        'date_fin' => Time::createFromFormat("d/m/Y", $sejourAPI[$sejourKey]['date_fin_sejour']),
                        'id_personne' => $sejourAPI[$sejourKey]['personne']['id_personne'],
                    ];
                    // Si le sujet de thèse est défini, l'ajouter aux données de mise à jour
                    if (isset($sejourAPI[$sejourKey]['these']['sujet_these'])) {
                        $update += ['sujet' => $sejourAPI[$sejourKey]['these']['sujet_these']];
                    } // Si le sujet de stage est défini, l'ajouter aux données de mise à jour
                    else if (isset($sejourAPI[$sejourKey]['stage']['sujet_stage'])) {
                        $update += ['sujet' => $sejourAPI[$sejourKey]['stage']['sujet_stage']];
                    }
                    // Mettre à jour le séjour dans la base de données
                    $this->sejourModel->updateSejour($sejourBDD->id_sejour, $update);

                    // Mettre à jour le statut de la personne si les informations sont fournies
                    if (isset($sejourAPI[$sejourKey]['statut']['id_statut']) && isset($sejourAPI[$sejourKey]['personne']['id_personne'])) {
                        $updateStatut = ['statut' => $sejourAPI[$sejourKey]['statut']['id_statut']];
                        $this->personneModel->updatePersonne($sejourAPI[$sejourKey]['personne']['id_personne'], $updateStatut);
                    }
                }
            }

            // Parcourir chaque séjour dans le tableau des séjours de l'API
            foreach ($sejourAPI as $sejour) {
                $id_personne = $sejour['personne']['id_personne'];
                // Préparer les données pour l'insertion
                $insert = [
                    'id_sejour' => $sejour['id_sejour'],
                    'date_debut' => Time::createFromFormat("d/m/Y", $sejour['date_debut_sejour']),
                    'date_fin' => Time::createFromFormat("d/m/Y", $sejour['date_fin_sejour']),
                    'id_personne' => $id_personne
                ];
                // Si le sujet de thèse est défini, l'ajouter aux données d'insertion
                if (isset($sejour['these']['sujet_these'])) {
                    $insert += ['sujet' => $sejour['these']['sujet_these']];
                } // Si le sujet de stage est défini, l'ajouter aux données d'insertion
                else if (isset($sejour['stage']['sujet_stage'])) {
                    $insert += ['sujet' => $sejour['stage']['sujet_stage']];
                }

                // Si le séjour n'existe pas encore dans la base de données et que la personne associée existe,
                // insérer le nouveau séjour dans la base de données
                if (!$this->sejourModel->getSejour($sejour['id_sejour'])
                    && $this->personneModel->getPersonne($id_personne)) {
                    $this->sejourModel->insertSejour($insert);
                }
            }
        }
    }

    /**
     * Fonction de mise à jour de tous les financements en base de données
     * @param $financementAPI
     * @return void
     */
    public function updateFinancementDB($financementAPI)
    {
        // Si le tableau des financements de l'API est vide, supprimer tous les financements de la base de données
        if (empty($financementAPI)) {
            $this->financementModel->deleteAll();
        } else {
            // Récupérer tous les financements existants dans la base de données
            $result = $this->financementModel->getAllFinancements();

            // Parcourir chaque financement existant dans la base de données
            foreach ($result as $financementBDD) {
                // Rechercher l'ID du financement dans le tableau des financements de l'API
                $mailKey = array_search($financementBDD->id_financement, array_column($financementAPI, 'id_financement'));

                // Si le financement n'existe pas dans l'API, le supprimer de la base de données
                if ($mailKey === false) {
                    $this->financementModel->deleteFinancement($financementBDD->id_financement);
                } else {
                    // Sinon, préparer les données pour la mise à jour
                    $data = [
                        'id_sejour' => $financementAPI[$mailKey]['id_sejour'],
                        'id_employeur' => $financementAPI[$mailKey]['org_payeur']['id_org_payeur']
                    ];
                    // Mettre à jour le financement dans la base de données
                    $this->financementModel->updateFinancement($financementBDD->id_financement, $data);
                }
            }

            // Parcourir chaque financement dans le tableau des financements de l'API
            foreach ($financementAPI as $financement) {
                $id_financement = $financement['id_financement'];
                $id_sejour = $financement['id_sejour'];
                $id_employeur = $financement['org_payeur']['id_org_payeur'];
                // Préparer les données pour l'insertion
                $insert = [
                    'id_financement' => $id_financement,
                    'id_sejour' => $id_sejour,
                    'id_employeur' => $id_employeur
                ];

                // Si le financement n'existe pas encore dans la base de données et que le séjour et l'employeur associés existent,
                // insérer le nouveau financement dans la base de données
                if (!$this->financementModel->getFinancement($id_financement)
                    && $this->sejourModel->getSejour($id_sejour)
                    && $this->employeurModel->getEmployeur($id_employeur)) {
                    $this->financementModel->insertFinancement($insert);
                }
            }
        }
    }

    /**
     * Fonction qui met à jour tous les rattachements en base de données
     * @param $rattachementsAPI
     * @return void
     */
    public function updateRattachementDB($rattachementsAPI)
    {
        // Si le tableau des rattachements de l'API est vide, supprimer tous les rattachements de la base de données
        if (empty($rattachementsAPI)) {
            $this->rattachementModel->deleteAll();
        } else {
            // Récupérer tous les rattachements existants dans la base de données
            $result = $this->rattachementModel->getAllRattachements();

            // Parcourir chaque rattachement existant dans la base de données
            foreach ($result as $rattachementBDD) {
                // Rechercher l'ID du rattachement dans le tableau des rattachements de l'API
                $rattachementKey = array_search($rattachementBDD->id_rattachement, array_column($rattachementsAPI, 'id_rattachement'));

                // Si le rattachement n'existe pas dans l'API, le supprimer de la base de données
                if ($rattachementKey === false) {
                    $this->rattachementModel->deleteRattachement($rattachementBDD->id_rattachement);
                } else {
                    // Sinon, préparer les données pour la mise à jour
                    $update = [
                        'id_sejour' => $rattachementsAPI[$rattachementKey]['id_sejour'],
                        'id_equipe' => $rattachementsAPI[$rattachementKey]['id_groupe']
                    ];
                    // Mettre à jour le rattachement dans la base de données
                    $this->rattachementModel->updateRattachement($rattachementBDD->id_rattachement, $update);
                }
            }

            // Parcourir chaque rattachement dans le tableau des rattachements de l'API
            foreach ($rattachementsAPI as $rattachement) {
                $insert = [
                    'id_rattachement' => $rattachement['id_rattachement'],
                    'id_sejour' => $rattachement['id_sejour'],
                    'id_equipe' => $rattachement['id_groupe']
                ];

                // Si le rattachement n'existe pas encore dans la base de données et que le séjour et l'équipe associés existent,
                // insérer le nouveau rattachement dans la base de données
                if (!$this->rattachementModel->getRattachement($rattachement['id_rattachement'])
                    && $this->sejourModel->getSejour($rattachement['id_sejour'])
                    && $this->equipeModel->getEquipe($rattachement['id_groupe'])) {
                    $this->rattachementModel->insertRattachement($insert);
                }
            }
        }
    }

    /**
     * Fonction de création des photos de profile des personnes
     * @param $profilePicturesAPI
     * @return void
     */
    public function createProfilePictures($profilePicturesAPI)
    {
        // TODO : faire photo pour chercheur invité

        // Obtenir la liste des fichiers dans le répertoire des photos de profil validées
        $fileFOLDER = array_diff(scandir(FCPATH . 'assets/images/profile/valide'), array('.', '..'));

        // Si le tableau des photos de profil de l'API est vide, supprimer toutes les photos du répertoire
        if (empty($profilePicturesAPI)) {
            foreach ($fileFOLDER as $file) {
                if (is_file(FCPATH . 'assets/images/profile/valide/' . $file)) {
                    unlink(FCPATH . 'assets/images/profile/valide/' . $file);
                }
            }
        } else {
            // Supprimer une photo du répertoire si l'ID ne correspond à aucun ID de personne sur l'API
            foreach ($fileFOLDER as $file) {
                $fileID = intval(explode(".", $file)[0]);
                $photoKey = in_array($fileID, array_column($profilePicturesAPI, 'id_personne'));
                if ($photoKey === false) {
                    unlink(FCPATH . 'assets/images/profile/valide/' . $file);
                }
            }

            // Modifier ou ajouter la photo de la personne en fonction des données de l'API
            foreach ($profilePicturesAPI as $profilePicture) {
                if (isset($profilePicture['photo'])) {
                    // Si une URL de photo est fournie, télécharger et sauvegarder la photo
                    file_put_contents(FCPATH . 'assets/images/profile/valide/' . $profilePicture['id_personne'] . '.jpg',
                        file_get_contents($profilePicture['photo']));
                } else if ($profilePicture['statut'] === 'Stagiaire') {
                    // Si le statut de la personne est 'Stagiaire', utiliser l'image par défaut pour les stagiaires
                    file_put_contents(FCPATH . 'assets/images/profile/valide/' . $profilePicture['id_personne'] . '.jpg',
                        file_get_contents(FCPATH . 'assets/images/profile/stagiaire.png'));
                } else if ($profilePicture['statut'] === 'Visiteur') {
                    // Si le statut de la personne est 'Stagiaire', utiliser l'image par défaut pour les stagiaires
                    file_put_contents(FCPATH . 'assets/images/profile/valide/' . $profilePicture['id_personne'] . '.jpg',
                        file_get_contents(FCPATH . 'assets/images/profile/visiteur.png'));
                } else {
                    // Utiliser l'image de profil par défaut pour les autres cas
                    file_put_contents(FCPATH . 'assets/images/profile/valide/' . $profilePicture['id_personne'] . '.jpg',
                        file_get_contents(FCPATH . 'assets/images/profile/default_profile.jpg'));
                }
            }
        }
    }

    /**
     * Fonction de mse à jour de tous les logins des personnes en base de données
     * @param $loginAPI
     * @return void
     */
    public function updateLoginDB($loginAPI)
    {
        // Si le tableau des logins de l'API est vide, mettre tous les logins à NULL dans la base de données
        if (empty($loginAPI)) {
            $update = [
                'login' => NULL
            ];
            $this->personneModel->updateAll($update);
        } else {
            // Parcourir chaque login dans le tableau des logins de l'API
            foreach ($loginAPI as $login) {
                $update = [
                    'login' => $login['login']
                ];

                // Mettre à jour le login de la personne dans la base de données
                $this->personneModel->updatePersonne($login['id_personne'], $update);
            }
        }
    }

    /**
     * Fonction de mise à jour de tous les encadrants en base de données
     * @param $encadrantsAPI
     * @return void
     */
    public function updateEncadrantDB($encadrantsAPI)
    {
        // Si le tableau des encadrants de l'API est vide, supprimer tous les encadrants de la base de données
        if (empty($encadrantsAPI)) {
            $this->encadrantModel->deleteAll();
        } else {
            // Récupérer tous les encadrants existants dans la base de données
            $result = $this->encadrantModel->getAllEncadrants();

            // Parcourir chaque encadrant existant dans la base de données
            foreach ($result as $encadrantBDD) {
                // Rechercher l'ID de l'encadrant dans le tableau des encadrants de l'API
                $encadrantKey = array_search($encadrantBDD->id_encadrant, array_column($encadrantsAPI, 'id_encadrant'));

                // Si l'encadrant n'existe pas dans l'API, le supprimer de la base de données
                if ($encadrantKey === false) {
                    $this->encadrantModel->deleteEncadrant($encadrantBDD->id_encadrant);
                } else {
                    // Sinon, préparer les données pour la mise à jour
                    $update = [
                        'id_sejour' => $encadrantsAPI[$encadrantKey]['id_sejour'],
                        'nom' => $encadrantsAPI[$encadrantKey]['nom'],
                        'prenom' => $encadrantsAPI[$encadrantKey]['prenom']
                    ];

                    // Si l'ID de la personne est défini, l'ajouter aux données de mise à jour, sinon mettre à NULL
                    if (isset($encadrantsAPI[$encadrantKey]['personne']['id_personne'])) {
                        $update += ['id_personne' => $encadrantsAPI[$encadrantKey]['personne']['id_personne']];
                    } else {
                        $update += ['id_personne' => NULL];
                    }

                    // Mettre à jour l'encadrant dans la base de données
                    $this->encadrantModel->updateEncadrant($encadrantBDD->id_encadrant, $update);
                }
            }

            // Parcourir chaque encadrant dans le tableau des encadrants de l'API
            foreach ($encadrantsAPI as $encadrant) {
                $insert = [
                    'id_encadrant' => $encadrant['id_encadrant'],
                    'id_sejour' => $encadrant['id_sejour'],
                    'nom' => $encadrant['nom'],
                    'prenom' => $encadrant['prenom']
                ];

                // Si l'ID de la personne est défini, l'ajouter aux données d'insertion, sinon mettre à NULL
                if (isset($encadrant['personne']['id_personne'])) {
                    $insert += ['id_personne' => $encadrant['personne']['id_personne']];
                    $existPersonne = $this->personneModel->getPersonne($encadrant['personne']['id_personne']) !== null;
                } else {
                    $insert += ['id_personne' => null];
                    $existPersonne = true;
                }

                // Si l'encadrant n'existe pas encore dans la base de données et que le séjour associé existe,
                // insérer le nouvel encadrant dans la base de données
                if (!$this->encadrantModel->getEncadrant($encadrant['id_encadrant'])
                    && $this->sejourModel->getSejour($encadrant['id_sejour'])
                    && $existPersonne) {
                    $this->encadrantModel->insertEncadrant($insert);
                }
            }
        }
    }

    public function beautifulPrint($data)
    {
        print("<pre>" . print_r($data, true) . "</pre>");
    }
}
