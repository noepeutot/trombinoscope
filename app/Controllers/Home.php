<?php

namespace App\Controllers;

use App\Models\APIModel;
use App\Models\EmployeurModel;
use App\Models\EncadrantModel;
use App\Models\EquipeModel;
use App\Models\FinancementModel;
use App\Models\MailModel;
use App\Models\PersonneModel;
use App\Models\ResponsabiliteModel;
use App\Models\SejourModel;
use App\Models\StatutModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;
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
    }

    /**
     * @return string
     */
    public function index(): string
    {
//        $this->updateDB();

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
        $insertImages = [];

        $allEncadrants = $this->getAllDataFromURL('encadrants');
        $allLocalisations = $this->getAllDataFromURL('localisation_personnels');
        $allPersonnels = $this->getAllDataFromURL('personnels');
        $allSejours = $this->getAllDataFromURL('sejours');
        $allMails = $this->getAllDataFromURL('mails_pro');
        $allResponsabilites = $this->getAllDataFromURL('personne_responsabilites');
        $allEmployeurs = $this->getAllDataFromURL('org_payeurs');
        $allStatuts = $this->getAllDataFromURL('statuts');
        $allEquipes = $this->getAllDataFromURL('groupes');
        $allPersonnes = $this->getAllDataFromURL('personnes');
        $allFinancements = $this->getAllDataFromURL('financements');

        if (isset($allPersonnels)) {
            $this->updatePersonnelDB($allPersonnels);
        }

        if (isset($allLocalisations)) {
            $this->updateLocalisationDB($allLocalisations);
        }

        if (isset($allResponsabilites)) {
            $this->updateResponsabiliteDB($allResponsabilites);
        }

        if (isset($allEmployeurs)) {
            $this->updateEmployeurDB($allEmployeurs);
        }

        if (isset($allMails)) {
            $this->updateMailDB($allMails);
        }

        if (isset($allSejours)) {
            $this->updateSejourBD($allSejours);
        }

        if (isset($allEncadrants)) {
            $this->updateEncadrantDB($allEncadrants);
        }

        if (isset($allStatuts)) {
            $this->updateStatusDB($allStatuts);
        }

        if (isset($allEquipes)) {
            $this->updateEquipeDB($allEquipes);
        }

        if (isset($allFinancements)) {
            $this->updateFinancementDB($allFinancements);
        }

        if (isset($allPersonnes)) {
            foreach ($allPersonnes as $personne) {
                $insertImages[] = ['id_personne' => $personne['id_personne'],
                    'photo' => $personne['photo']];
            }
            $this->createProfilePictures($insertImages);
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
     * Fonction de mise à jour de tous les employeurs en base de données
     * @param $employeursArray
     * @return void
     */
    public function updateEmployeurDB($employeursArray)
    {
        if (empty($employeursArray)) {
            $this->employeurModel->deleteAll();
        } else {
            $result = $this->employeurModel->getAllEmployeurs();

            foreach ($result as $employeurBDD) {
                $employeurKey = array_search($employeurBDD->id_employeur,
                    array_column($employeursArray, 'id_org_payeur'));
                if ($employeurKey === false) {
                    $this->employeurModel->deleteEmployeur($employeurBDD->id_employeur);
                } else {
                    $update = [
                        'nom' => $employeursArray[$employeurKey]['organisme_payeur'],
                        'nom_court' => $employeursArray[$employeurKey]['nom_court_op']
                    ];
                    $this->employeurModel->updateEmployeur($employeurBDD->id_employeur, $update);
                }
            }

            foreach ($employeursArray as $employeur) {
                $insert = [
                    'id_employeur' => $employeur['id_org_payeur'],
                    'nom' => $employeur['organisme_payeur'],
                    'nom_court' => $employeur['nom_court_op']
                ];
                if (!$this->employeurModel->getEmployeur($employeur['id_org_payeur'])) {
                    $this->employeurModel->insertEmployeur($insert);
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
        if (empty($mailsAPI)) {
            $this->mailModel->deleteAll();
        } else {
            $result = $this->mailModel->getAllMails();

            foreach ($result as $mailBDD) {
                $mailKey = array_search($mailBDD->id_mail,
                    array_column($mailsAPI, 'id_mail'));
                if ($mailKey === false) {
                    $this->mailModel->deleteMail($mailBDD->id_mail);
                } else {
                    $data = [
                        'libelle' => $mailsAPI[$mailKey]['mail'],
                        'type' => $mailsAPI[$mailKey]['type_mail']['type_mail'],
                        'id_personne' => $mailsAPI[$mailKey]['personne']['id_personne']
                    ];
                    $this->mailModel->updateMail($mailBDD->id_mail, $data);
                }
            }

            foreach ($mailsAPI as $mail) {
                if ($mail['type_mail']['type_mail']!='Perso') {
                    $insert = [
                        'id_mail' => $mail['id_mail'],
                        'libelle' => $mail['mail'],
                        'type' => $mail['type_mail']['type_mail'],
                        'id_personne' => $mail['personne']['id_personne']
                    ];

                    if (!$this->mailModel->getMail($mail['id_mail'])) {
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
        if (empty($sejourAPI)) {
            /**
             * TRIGGER sur employeur_sejour mis en place lors du delete d’un séjour
             */
            $this->sejourModel->deleteAll();
        } else {
            $result = $this->sejourModel->getAllSejours();

            foreach ($result as $sejourBDD) {
                $sejourKey = array_search($sejourBDD->id_sejour,
                    array_column($sejourAPI, 'id_sejour'));
                if ($sejourKey === false) {
                    $this->sejourModel->deleteSejour($sejourBDD->id_sejour);
                } else {
                    $update = [
                        'date_debut' => Time::createFromFormat("d/m/Y", $sejourAPI[$sejourKey]['date_debut_sejour']),
                        'date_fin' => Time::createFromFormat("d/m/Y", $sejourAPI[$sejourKey]['date_fin_sejour']),
                        'id_personne' => $sejourAPI[$sejourKey]['personne']['id_personne'],
                    ];
                    if (isset($sejourAPI[$sejourKey]['these']['sujet_these'])) {
                        $update += ['sujet' => $sejourAPI[$sejourKey]['these']['sujet_these']];
                    } else if (isset($sejourAPI[$sejourKey]['stage']['sujet_stage'])) {
                        $update += ['sujet' => $sejourAPI[$sejourKey]['stage']['sujet_stage']];
                    }
                    $this->sejourModel->updateSejour($sejourBDD->id_sejour, $update);
                }
            }

            foreach ($sejourAPI as $sejour) {
                $insert = [
                    'id_sejour' => $sejour['id_sejour'],
                    'date_debut' => Time::createFromFormat("d/m/Y", $sejour['date_debut_sejour']),
                    'date_fin' => Time::createFromFormat("d/m/Y", $sejour['date_fin_sejour']),
                    'id_personne' => $sejour['personne']['id_personne']
                ];
                if (isset($sejour['these']['sujet_these'])) {
                    $insert += ['sujet' => $sejour['these']['sujet_these']];
                } else if (isset($sejour['stage']['sujet_stage'])) {
                    $insert += ['sujet' => $sejour['stage']['sujet_stage']];
                }

                if (!$this->sejourModel->getSejour($sejour['id_sejour'])) {
                    $this->sejourModel->insertSejour($insert);
                }
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
        if (empty($encadrantsAPI)) {
            $this->encadrantModel->deleteAll();
        } else {
            $result = $this->encadrantModel->getAllEncadrants();

            foreach ($result as $encadrantBDD) {
                $encadrantKey = array_search($encadrantBDD->id_encadrant,
                    array_column($encadrantsAPI, 'id_encadrant'));
                if ($encadrantKey === false) {
                    $this->encadrantModel->deleteEncadrant($encadrantBDD->id_encadrant);
                } else {
                    $update = [
                        'id_sejour' => $encadrantsAPI[$encadrantKey]['id_sejour'],
                        'nom' => $encadrantsAPI[$encadrantKey]['nom'],
                        'prenom' => $encadrantsAPI[$encadrantKey]['prenom']
                    ];

                    if (isset($encadrantsAPI[$encadrantKey]['personne']['id_personne'])) {
                        $update += ['id_personne' => $encadrantsAPI[$encadrantKey]['personne']['id_personne']];
                    } else {
                        $update += ['id_personne' => NULL];
                    }

                    $this->encadrantModel->updateEncadrant($encadrantBDD->id_encadrant, $update);
                }
            }

            foreach ($encadrantsAPI as $encadrant) {
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

                if (!$this->encadrantModel->getEncadrant($encadrant['id_encadrant'])) {
                    $this->encadrantModel->insertEncadrant($insert);
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
        if (empty($statusAPI)) {
            $this->statutModel->deleteAll();
        } else {
            $result = $this->statutModel->getAllStatuts();

            foreach ($result as $statutDB) {
                $statutKey = array_search($statutDB->id_statut,
                    array_column($statusAPI, 'id_statut'));
                if ($statutKey === false) {
                    $this->statutModel->deleteStatut($statutDB->id_statut);
                } else {
                    $update = [
                        'statut' => $statusAPI[$statutKey]['statut']
                    ];
                    $this->statutModel->updateStatut($statutDB->id_statut, $update);
                }
            }

            foreach ($statusAPI as $statut) {
                $insert = [
                    'id_statut' => $statut['id_statut'],
                    'statut' => $statut['statut']
                ];
                if (!$this->statutModel->getStatut($statut['id_statut'])) {
                    $this->statutModel->insertStatut($insert);
                }
            }
        }
    }

    /**
     * Fonction de mise à jour de toutes les équipes en base de données
     * @param $equipesAPI
     * @return void
     */
    public function updateEquipeDB($equipesAPI)
    {
        if (empty($equipesAPI)) {
            $this->equipeModel->deleteAll();
        } else {
            $result = $this->equipeModel->getAllEquipes();

            foreach ($result as $equpeDB) {
                $equipeKey = array_search($equpeDB->id_equipe,
                    array_column($equipesAPI, 'id_equipe'));
                if ($equipeKey === false) {
                    $this->equipeModel->deleteEquipe($equpeDB->id_equipe);
                } else {
                    $update = [
                        'nom_court' => $equipesAPI[$equipeKey]['nom_court_groupe'],
                        'nom_long' => $equipesAPI[$equipeKey]['nom_long_groupe']
                    ];
                    $this->equipeModel->updateEquipe($equpeDB->id_equipe, $update);
                }
            }

            foreach ($equipesAPI as $equipe) {
                $insert = [
                    'id_equipe' => $equipe['id_groupe'],
                    'nom_court' => $equipe['nom_court_groupe'],
                    'nom_long' => $equipe['nom_long_groupe']
                ];

                if (!$this->equipeModel->getEquipe($equipe['id_groupe'])) {
                    $this->equipeModel->insertEquipe($insert);
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
        if (empty($financementAPI)) {
            $this->financementModel->deleteAll();
        } else {
            $result = $this->financementModel->getAllFinancements();

            foreach ($result as $financementBDD) {
                $mailKey = array_search($financementBDD->id_financement,
                    array_column($financementAPI, 'id_financement'));
                if ($mailKey === false) {
                    $this->financementModel->deleteFinancement($financementBDD->id_financement);
                } else {
                    $data = [
                        'id_sejour' => $financementAPI[$mailKey]['id_sejour'],
                        'id_employeur' => $financementAPI[$mailKey]['org_payeur']['id_org_payeur']
                    ];
                    $this->financementModel->updateFinancement($financementBDD->id_financement, $data);
                }
            }

            foreach ($financementAPI as $financement) {
                $insert = [
                    'id_financement' => $financement['id_financement'],
                    'id_sejour' => $financement['id_sejour'],
                    'id_employeur' => $financement['org_payeur']['id_org_payeur']
                ];

                if (!$this->financementModel->getFinancement($financement['id_financement'])) {
                    $this->financementModel->insertFinancement($insert);
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
        $fileFOLDER = array_diff(scandir('assets/images/profile'), array('.', '..'));
        if (empty($profilePicturesAPI)) {
            foreach ($fileFOLDER as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        } else {
            // Supprime une photo du répertoire si l’ID ne correspond à aucun ID de personne sur l’API
            foreach ($fileFOLDER as $file) {
                $fileID = intval(explode(".", $file)[0]);
                $photoKey = in_array($fileID,
                    array_column($profilePicturesAPI, 'id_personne'));
                if ($photoKey === false) {
                    unlink('assets/images/profile/' . $file);
                }
            }

            // Modifie ou ajoute la photo de la personne et si elle n’existe pas, modifie avec une photo par défaut
            foreach ($profilePicturesAPI as $profilePicture) {
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
        return $this->personneModel->searchPersonne($query, $statuts, $equipes, $tuteurs);
    }

    /**
     * Fonction qui permet de retourner les statuts en base de données
     * @return array
     */
    public function getStatuts(): array
    {
        return $this->statutModel->getAllStatuts();
    }

    /**
     * Fonction qui permet de retourner les équipes en base de données
     * @return array
     */
    public function getEquipes(): array
    {
        return $this->equipeModel->getAllEquipes();
    }

    /**
     * Fonction qui permet de retourner les tuteurs en base de données
     * @return array
     */
    public function getEncadrants(): array
    {
        return $this->personneModel->getAllEncadrants();
    }

    public function beautifulPrint($data)
    {
        print("<pre>" . print_r($data, true) . "</pre>");
    }
}
