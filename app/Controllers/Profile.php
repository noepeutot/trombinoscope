<?php

namespace App\Controllers;

use App\Models\APIModel;
use App\Models\BureauModel;
use App\Models\EmployeurModel;
use App\Models\EncadrantModel;
use App\Models\EquipeModel;
use App\Models\FinancementModel;
use App\Models\MailModel;
use App\Models\ModificationModel;
use App\Models\PersonneModel;
use App\Models\ResponsabiliteModel;
use App\Models\SejourModel;
use App\Models\StatutModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Session\Session;
use Config\Services;
use Psr\Log\LoggerInterface;

class Profile extends BaseController
{
    protected int $personneID;
    protected APIModel $ApiModel;
    protected PersonneModel $personneModel;
    protected ResponsabiliteModel $responsabiliteModel;
    protected MailModel $mailModel;
    protected EmployeurModel $employeurModel;
    protected SejourModel $sejourModel;
    protected FinancementModel $financementModel;
    protected EncadrantModel $encadrantModel;
    protected StatutModel $statutModel;
    protected BureauModel $bureauModel;
    protected EquipeModel $equipeModel;
    protected ModificationModel $modificationModel;

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
        $this->bureauModel = new BureauModel();
        $this->equipeModel = new EquipeModel();
        $this->modificationModel = new ModificationModel();

        $this->session = Services::session();
    }

    //TODO : commentaire pour modif
    public function index($id): string
    {
        return $this->profile($id);
    }

    /** Fonction qui permet de gérer l’affichage du profile
     * @param $id
     * @return string
     */
    public function profile($id): string
    {
        $data = [];

        $user = $this->session->get('user');
        if ($user) {
            $personneConnectee = $this->personneModel->getPersonneLogin($user['login']);
            if ($personneConnectee) {
                $data['personneConnectee'] = $personneConnectee;
            }
        }

        $this->personneID = $id;

        $personne = $this->personneModel->getPersonne($this->personneID);
        $mails = $this->mailModel->getMailPersonne($this->personneID);

        $employeurs = $this->employeurModel->getEmployeurPersonne($this->personneID);

        $sejour = $this->sejourModel->getSejourPersonne($this->personneID);

        $responsabilites = $this->responsabiliteModel->getResponsabilitePersonne($this->personneID);

        $statut = $this->statutModel->getStatutPersonne($this->personneID);

        $equipes = $this->equipeModel->getEquipePersonne($this->personneID);

        $encadres = $this->personneModel->getEncadrePersonne($this->personneID);

        $bureau = $this->bureauModel->getBureauPersonne($this->personneID);

        if (!empty($personne)) {
            $data['personne'] = $personne;
        }

        if (!empty($mails)) {
            $data['mails'] = $mails;
        }

        if (!empty($employeurs)) {
            $data['employeurs'] = $employeurs;
        }

        if (!empty($responsabilites)) {
            $data['responsabilites'] = $responsabilites;
        }

        if (!empty($sejour)) {
            $data['sejour'] = $sejour;
        }

        if (!empty($statut)) {
            $data['statut'] = $statut;
        }

        if (!empty($equipes)) {
            $data['equipes'] = $equipes;
        }

        if (!empty($encadres)) {
            $data['encadres'] = $encadres;
        }

        if (!empty($bureau)) {
            $data['bureau'] = $bureau;
        }

        if (!empty($sejour) && !empty($personne)) {
            $data['responsables'] = $this->personneModel->getResponsablePersonne($personne->id_personne, $sejour->id_sejour);
        }

        return view('profile', $data);
    }

    public function edit()
    {
        $data = [];

        $user = $this->session->get('user');
        if ($user) {
            $personneConnectee = $this->personneModel->getPersonneLogin($user['login']);
            if ($personneConnectee) {
                $data['personneConnectee'] = $personneConnectee;
                $this->personneID = $personneConnectee->id_personne;

                $personne = $this->personneModel->getPersonne($this->personneID);
                $mailPersonne = $this->mailModel->getMailPersonne($this->personneID);
                $employeursPersonne = $this->employeurModel->getEmployeurPersonne($this->personneID);
                $sejourPersonne = $this->sejourModel->getSejourPersonne($this->personneID);
                $responsabilitesPersonne = $this->responsabiliteModel->getResponsabilitePersonne($this->personneID);
                $statutPersonne = $this->statutModel->getStatutPersonne($this->personneID);
                $equipePersonne = $this->equipeModel->getEquipePersonne($this->personneID);
                $encadresPersonne = $this->personneModel->getEncadrePersonne($this->personneID);
                $bureauPersonne = $this->bureauModel->getBureauPersonne($this->personneID);


                // Vérification si appel du formulaire
                if ($this->request->getGetPost()) {
                    // Ajout, modification ou suppréssion des modifications sur le profile
                    $commentaire = "";
                    if ($this->request->getGetPost('commentaire')) {
                        $commentaire = $this->request->getGetPost('commentaire');
                    }

                    if ($this->request->getGetPost('nom')) {
                        $nom = $this->request->getGetPost('nom');
                        $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'nom');
                        if ($personne->nom !== $nom && empty($modification)) {
                            $insert = [
                                'id_personne' => $this->personneID,
                                'attribut' => "nom",
                                'avant' => $personne->nom,
                                'apres' => $nom,
                                'statut' => "attente",
                                'commentaire' => $commentaire
                            ];
                            $this->modificationModel->insertModification($insert);
                        } else if ($personne->nom !== $nom && !empty($modification)) {
                            $update = [
                                'apres' => $nom,
                                'commentaire' => $commentaire
                            ];
                            $this->modificationModel->updateModification($modification->id_modification, $update);
                        } else if ($personne->nom == $nom && !empty($modification)) {
                            $this->modificationModel->deleteModification($modification->id_modification);
                        }
                    }
                    if ($this->request->getGetPost('prenom')) {
                        $prenom = $this->request->getGetPost('prenom');
                        $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'prenom');
                        if ($personne->prenom !== $prenom && empty($modification)) {
                            $insert = [
                                'id_personne' => $this->personneID,
                                'attribut' => "prenom",
                                'avant' => $personne->prenom,
                                'apres' => $prenom,
                                'statut' => "attente",
                                'commentaire' => $commentaire
                            ];
                            $this->modificationModel->insertModification($insert);
                        } else if ($personne->prenom !== $prenom && !empty($modification)) {
                            $update = [
                                'apres' => $prenom,
                                'commentaire' => $commentaire
                            ];
                            $this->modificationModel->updateModification($modification->id_modification, $update);
                        } else if ($personne->prenom == $prenom && !empty($modification)) {
                            $this->modificationModel->deleteModification($modification->id_modification);
                        }
                    }
                    if ($this->request->getGetPost('email')) {
                        $email = $this->request->getGetPost('email');
                        $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'mail');
                        if ($mailPersonne->libelle !== $email && empty($modification)) {
                            $insert = [
                                'id_personne' => $this->personneID,
                                'attribut' => "mail",
                                'avant' => $mailPersonne->libelle,
                                'apres' => $email,
                                'statut' => "attente",
                                'commentaire' => $commentaire
                            ];
                            $this->modificationModel->insertModification($insert);
                        } else if ($mailPersonne->libelle !== $email && !empty($modification)) {
                            $update = [
                                'apres' => $email,
                                'commentaire' => $commentaire
                            ];
                            $this->modificationModel->updateModification($modification->id_modification, $update);
                        } else if ($mailPersonne->libelle == $email && !empty($modification)) {
                            $this->modificationModel->deleteModification($modification->id_modification);
                        }
                    }
                    if ($this->request->getGetPost('telephone')) {
                        $telephone = $this->request->getGetPost('telephone');
                        $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'telephone');
                        if ($personne->telephone !== $telephone && empty($modification)) {
                            $insert = [
                                'id_personne' => $this->personneID,
                                'attribut' => "telephone",
                                'avant' => $personne->telephone,
                                'apres' => $telephone,
                                'statut' => "attente",
                                'commentaire' => $commentaire
                            ];
                            $this->modificationModel->insertModification($insert);
                        } else if ($personne->telephone !== $telephone && !empty($modification)) {
                            $update = [
                                'apres' => $telephone,
                                'commentaire' => $commentaire
                            ];
                            $this->modificationModel->updateModification($modification->id_modification, $update);
                        } else if ($personne->telephone == $telephone && !empty($modification)) {
                            $this->modificationModel->deleteModification($modification->id_modification);
                        }
                    }
                    if ($this->request->getGetPost('bureau')) {
                        $bureau = intval($this->request->getGetPost('bureau'));
                        $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'bureau');
                        if ($bureauPersonne->id_bureau !== $bureau && empty($modification)) {
                            $insert = [
                                'id_personne' => $this->personneID,
                                'attribut' => "bureau",
                                'avant' => $bureauPersonne->id_bureau,
                                'apres' => $bureau,
                                'statut' => "attente",
                                'commentaire' => $commentaire
                            ];
                            $this->modificationModel->insertModification($insert);
                        } else if ($bureauPersonne->id_bureau !== $bureau && !empty($modification)) {
                            $update = [
                                'apres' => $bureau,
                                'commentaire' => $commentaire
                            ];
                            $this->modificationModel->updateModification($modification->id_modification, $update);
                        } else if ($bureauPersonne->id_bureau == $bureau && !empty($modification)) {
                            $this->modificationModel->deleteModification($modification->id_modification);
                        }
                    }
                    if ($this->request->getGetPost('statut')) {
                        $statut = intval($this->request->getGetPost('statut'));
                        $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'statut');
                        if ($statutPersonne->id_statut !== $statut && empty($modification)) {
                            $insert = [
                                'id_personne' => $this->personneID,
                                'attribut' => "statut",
                                'avant' => $statutPersonne->id_statut,
                                'apres' => $statut,
                                'statut' => "attente",
                                'commentaire' => $commentaire
                            ];
                            $this->modificationModel->insertModification($insert);
                        } else if ($statutPersonne->id_statut !== $statut && !empty($modification)) {
                            $update = [
                                'apres' => $statut,
                                'commentaire' => $commentaire
                            ];
                            $this->modificationModel->updateModification($modification->id_modification, $update);
                        } else if ($statutPersonne->id_statut == $statut && !empty($modification)) {
                            $this->modificationModel->deleteModification($modification->id_modification);
                        }
                    }
                    if ($this->request->getGetPost('equipe[]')) {
                        $equipesAPRES = $this->request->getGetPost('equipe[]');
                        $equipeINT = [];
                        foreach ($equipesAPRES as $eq) {
                            $equipeINT[] = intval($eq);
                        }
                        $equipesAPRES = $equipeINT;

                        $equipesAVANT = [];
                        foreach ($equipePersonne as $equipeP) {
                            $equipesAVANT[] = $equipeP->id_equipe;
                        }

                        asort($equipesAVANT);
                        asort($equipesAPRES);

                        $listEquipeAPRES = implode(', ', $equipesAPRES);

                        $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'equipe');
                        if ($equipesAPRES != $equipesAVANT && empty($modification)) {
                            $listEquipeAVANT = implode(', ', $equipesAVANT);
                            $insert = [
                                'id_personne' => $this->personneID,
                                'attribut' => "equipe",
                                'avant' => $listEquipeAVANT,
                                'apres' => $listEquipeAPRES,
                                'statut' => "attente",
                                'commentaire' => $commentaire
                            ];
                            $this->modificationModel->insertModification($insert);
                        } else if ($equipesAPRES != $equipesAVANT && !empty($modification)) {
                            $update = [
                                'apres' => $listEquipeAPRES,
                                'commentaire' => $commentaire
                            ];
                            $this->modificationModel->updateModification($modification->id_modification, $update);
                        } else if ($equipesAPRES == $equipesAVANT && !empty($modification)) {
                            $this->modificationModel->deleteModification($modification->id_modification);
                        }
                    }
                    if ($this->request->getGetPost('employeur[]')) {
                        $employeursAPRES = $this->request->getGetPost('employeur[]');
                        $employeurINT = [];
                        foreach ($employeursAPRES as $employeur) {
                            $employeurINT[] = intval($employeur);
                        }
                        $employeursAPRES = $employeurINT;

                        $employeursAVANT = [];
                        foreach ($employeursPersonne as $employeur) {
                            $employeursAVANT[] = $employeur->id_employeur;
                        }

                        asort($employeursAVANT);
                        asort($employeursAPRES);
                        $listEmployeurAPRES = implode(', ', $employeursAPRES);
                        $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'employeur');
                        if ($employeursAPRES != $employeursAVANT && empty($modification)) {
                            $listEmployeurAVANT = implode(', ', $employeursAVANT);
                            $insert = [
                                'id_personne' => $this->personneID,
                                'attribut' => "employeur",
                                'avant' => $listEmployeurAVANT,
                                'apres' => $listEmployeurAPRES,
                                'statut' => "attente",
                                'commentaire' => $commentaire
                            ];
                            $this->modificationModel->insertModification($insert);
                        } else if ($employeursAPRES != $employeursAVANT && !empty($modification)) {
                            $update = [
                                'apres' => $listEmployeurAPRES,
                                'commentaire' => $commentaire
                            ];
                            $this->modificationModel->updateModification($modification->id_modification, $update);
                        } else if ($employeursAPRES == $employeursAVANT && !empty($modification)) {
                            $this->modificationModel->deleteModification($modification->id_modification);
                        }
                    }
                    if ($this->request->getGetPost('activite')) {
                        $activite = $this->request->getGetPost('activite');
                        $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'activite');
                        if ($sejourPersonne->sujet !== $activite && empty($modification)) {
                            $insert = [
                                'id_personne' => $this->personneID,
                                'attribut' => "activite",
                                'avant' => $sejourPersonne->sujet,
                                'apres' => $activite,
                                'statut' => "attente",
                                'commentaire' => $commentaire
                            ];
                            $this->modificationModel->insertModification($insert);
                        } else if ($sejourPersonne->sujet !== $activite && !empty($modification)) {
                            $update = [
                                'apres' => $activite,
                                'commentaire' => $commentaire
                            ];
                            $this->modificationModel->updateModification($modification->id_modification, $update);
                        } else if ($sejourPersonne->sujet == $activite && !empty($modification)) {
                            $this->modificationModel->deleteModification($modification->id_modification);
                        }
                    }

//                    // TODO : gérer les encadrés
//                if ($this->request->getGetPost('encadre[]')) {
//                    $encadres = $this->request->getGetPost('encadre[]');
//                    var_dump($encadres);
//                }
                }

                $allEmployeurs = $this->employeurModel->getAllEmployeurs();
                $allBureaux = $this->bureauModel->getAllBureaux();
                $allStatuts = $this->statutModel->getAllStatuts();
                $allEquipes = $this->equipeModel->getAllEquipes();

                // Données des modifications en attente si existantes
                $nomModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'nom');
                $prenomModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'prenom');
                $mailModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'mail');
                $telephoneModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'telephone');
                $bureauModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'bureau');
                $statutModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'statut');
                $activiteModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'activite');
                $equipesIDModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'equipe');
                $equipesModif = [];
                if (!empty($equipesIDModif)) {
                    $equipesID = explode(', ', $equipesIDModif->apres);
                    foreach ($equipesID as $equipeID) {
                        $equipesModif[] = $this->equipeModel->getEquipe(intval($equipeID));
                    }
                }
                $employeursIDModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'employeur');
                $employeursModif = [];
                if (!empty($employeursIDModif)) {
                    $employeursID = explode(', ', $equipesIDModif->apres);
                    foreach ($employeursID as $employeurID) {
                        $employeursModif[] = $this->employeurModel->getEmployeur(intval($employeurID));
                    }
                }

                if (!empty($personne)) {
                    $data['personne'] = $personne;
                }

                if (!empty($statutPersonne)) {
                    $data['statutPersonne'] = $statutPersonne;
                }

                if (!empty($allStatuts)) {
                    $data['allStatuts'] = $allStatuts;
                }

                if (!empty($mailPersonne)) {
                    $data['mailPersonne'] = $mailPersonne;
                }

                if (!empty($employeursPersonne)) {
                    $data['employeursPersonne'] = $employeursPersonne;
                }

                if (!empty($allEmployeurs)) {
                    $data['allEmployeurs'] = $allEmployeurs;
                }

                if (!empty($responsabilitesPersonne)) {
                    $data['responsabilitesPersonne'] = $responsabilitesPersonne;
                }

                if (!empty($sejourPersonne)) {
                    $data['sejourPersonne'] = $sejourPersonne;
                    $data['responsablesPersonne'] = $this->personneModel->getResponsablePersonne($this->personneID, $sejourPersonne->id_sejour);
                }

                if (!empty($encadresPersonne)) {
                    $data['encadresPersonne'] = $encadresPersonne;
                }

                if (!empty($equipePersonne)) {
                    $data['equipePersonne'] = $equipePersonne;
                }

                if (!empty($allEquipes)) {
                    $data['allEquipes'] = $allEquipes;
                }

                if (!empty($bureauPersonne)) {
                    $data['bureauPersonne'] = $bureauPersonne;
                }

                if (!empty($allBureaux)) {
                    $data['allBureaux'] = $allBureaux;
                }

                if (!empty($nomModif)) {
                    $data['nomModif'] = $nomModif;
                }

                if (!empty($prenomModif)) {
                    $data['prenomModif'] = $prenomModif;
                }

                if (!empty($mailModif)) {
                    $data['mailModif'] = $mailModif;
                }

                if (!empty($telephoneModif)) {
                    $data['telephoneModif'] = $telephoneModif;
                }

                if (!empty($bureauModif)) {
                    $data['bureauModif'] = intval($bureauModif->apres);
                }

                if (!empty($statutModif)) {
                    $data['statutModif'] = intval($statutModif->apres);
                }

                if (!empty($activiteModif)) {
                    $data['activiteModif'] = $activiteModif;
                }

                if (!empty($equipesModif)) {
                    $data['equipesModif'] = $equipesModif;
                }

                if (!empty($employeursModif)) {
                    $data['employeursModif'] = $employeursModif;
                }

                return view('profile_edit', $data);
            }
        }
        return redirect('/');
    }

    public
    function beautifulPrint($data)
    {
        print("<pre>" . print_r($data, true) . "</pre>");
    }
}