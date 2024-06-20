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
use CodeIgniter\HTTP\RedirectResponse;
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

    public function index($id)
    {
        return $this->profile($id);
    }

    /** Fonction qui permet de gérer l’affichage du profile
     * @param $id
     * @return string | RedirectResponse
     */
    public function profile($id)
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

        if(!$personne) {
            return redirect('/');
        }
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

        return view('frontoffice/profile', $data);
    }

    public function deletePhoto()
    {
        $user = $this->session->get('user');
        if ($user) {
            $personneConnectee = $this->personneModel->getPersonneLogin($user['login']);
            if ($personneConnectee) {
                $this->personneID = $personneConnectee->id_personne;
                $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'photo');
                if ($modification) {
                    $this->modificationModel->deleteModification($modification->id_modification);
                }
                return $this->edit();
            }
        }
        return redirect('/');
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

                    $validationRule = [
                        'photo_profile' => [
                            'label' => 'Image File',
                            'rules' => 'mime_in[photo_profile,image/jpg,image/jpeg,image/png]|max_size[photo_profile,51200]',
                            'errors' => [
                                'max_size' => 'Veuillez choisir une image inférieure à 50 Mo.',
                                'mime_in' => 'Veuillez choisir une image valide du format png, jpg ou jpeg.'
                            ]
                        ],
                    ];

                    $image = $this->request->getFile('photo_profile');

                    if (!empty($image->getFileInfo()->getFilename())) {
                        if (!$this->validateData([], $validationRule)) {
                            $data['errors'][] = ['Erreur de modification de photo' => $this->validator->getError('photo_profile')];
                        } else if (!$image->isValid()) {
                            $data['errors'][] = "Erreur dans l’upload du fichier. Veuillez vérifier votre fichier et essayer ultérieurement.";
                        } else if (!$image->hasMoved()) {
                            $result = $image->move('assets/images/profile/en_attente',
                                $this->personneID . '.jpg', true);
                            if ($result) {
                                $data['success'][] = ["Modification de la photo de profile" => "Ajout de la photo avec succès !"];
                                $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Photo');
                                $imgUrl = img_url('');
                                if (empty($modification)) {
                                    $insert = [
                                        'id_personne' => $this->personneID,
                                        'attribut' => "Photo",
                                        'avant' => $imgUrl . 'profile/valide/' . $this->personneID,
                                        'apres' => $imgUrl . 'profile/en_attente/' . $this->personneID,
                                        'statut' => "attente",
                                        'commentaire' => $commentaire
                                    ];
                                    $this->modificationModel->insertModification($insert);
                                } else {
                                    $update = [
                                        'apres' => $imgUrl . 'profile/en_attente/' . $this->personneID,
                                        'commentaire' => $commentaire
                                    ];
                                    $this->modificationModel->updateModification($modification->id_modification, $update);
                                }
                            } else {
                                $data['errors'][] = "Echec de l’ajout de l’image. Veuillez vérifier votre fichier et essayer ultérieurement.";
                            }
                        } else {
                            $data['errors'][] = "Echec de l’ajout de l’image. Veuillez vérifier votre fichier et essayer ultérieurement.";
                        }
                    }

                    if ($this->request->getGetPost('nom')) {
                        $nom = $this->request->getGetPost('nom');
                        $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Nom');
                        if ($personne->nom !== $nom && empty($modification)) {
                            $insert = [
                                'id_personne' => $this->personneID,
                                'attribut' => "Nom",
                                'avant' => $personne->nom,
                                'apres' => $nom,
                                'statut' => "attente",
                                'commentaire' => $commentaire
                            ];
                            $result = $this->modificationModel->insertModification($insert);
                            if (!$result) {
                                $data['errors'][] = ['Modification du nom' => "La modification du nom n’a pas pu aboutir. Veuillez réessayer."];
                            }
                        } else if ($personne->nom !== $nom && !empty($modification)) {
                            $update = [
                                'apres' => $nom,
                                'commentaire' => $commentaire
                            ];
                            $result = $this->modificationModel->updateModification($modification->id_modification, $update);
                            if (!$result) {
                                $data['errors'][] = ['Modification du nom' => "La modification du nom n’a pas pu aboutir. Veuillez réessayer."];
                            }
                        } else if ($personne->nom == $nom && !empty($modification)) {
                            $this->modificationModel->deleteModification($modification->id_modification);
                        }
                    }
                    if ($this->request->getGetPost('prenom')) {
                        $prenom = $this->request->getGetPost('prenom');
                        $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Prénom');
                        if ($personne->prenom !== $prenom && empty($modification)) {
                            $insert = [
                                'id_personne' => $this->personneID,
                                'attribut' => "Prénom",
                                'avant' => $personne->prenom,
                                'apres' => $prenom,
                                'statut' => "attente",
                                'commentaire' => $commentaire
                            ];
                            $result = $this->modificationModel->insertModification($insert);
                            if (!$result) {
                                $data['errors'][] = ['Modification du prénom' => "La modification du prénom n’a pas pu aboutir. Veuillez réessayer."];
                            }
                        } else if ($personne->prenom !== $prenom && !empty($modification)) {
                            $update = [
                                'apres' => $prenom,
                                'commentaire' => $commentaire
                            ];
                            $result = $this->modificationModel->updateModification($modification->id_modification, $update);
                            if (!$result) {
                                $data['errors'][] = ['Modification du prénom' => "La modification du prénom n’a pas pu aboutir. Veuillez réessayer."];
                            }
                        } else if ($personne->prenom == $prenom && !empty($modification)) {
                            $this->modificationModel->deleteModification($modification->id_modification);
                        }
                    }
                    if ($this->request->getGetPost('email')) {
                        $email = $this->request->getGetPost('email');
                        $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Mail');
                        if ($mailPersonne->libelle !== $email && empty($modification)) {
                            $insert = [
                                'id_personne' => $this->personneID,
                                'attribut' => "Mail",
                                'avant' => $mailPersonne->libelle,
                                'apres' => $email,
                                'statut' => "attente",
                                'commentaire' => $commentaire
                            ];
                            $result = $this->modificationModel->insertModification($insert);
                            if (!$result) {
                                $data['errors'][] = ['Modification du mail' => "La modification du mail n’a pas pu aboutir. Veuillez réessayer."];
                            }
                        } else if ($mailPersonne->libelle !== $email && !empty($modification)) {
                            $update = [
                                'apres' => $email,
                                'commentaire' => $commentaire
                            ];
                            $result = $this->modificationModel->updateModification($modification->id_modification, $update);
                            if (!$result) {
                                $data['errors'][] = ['Modification du mail' => "La modification du mail n’a pas pu aboutir. Veuillez réessayer."];
                            }
                        } else if ($mailPersonne->libelle == $email && !empty($modification)) {
                            $this->modificationModel->deleteModification($modification->id_modification);
                        }
                    }
                    if ($this->request->getGetPost('telephone')) {
                        $telephone = $this->request->getGetPost('telephone');
                        $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Téléphone');
                        if ($personne->telephone !== $telephone && empty($modification)) {
                            $insert = [
                                'id_personne' => $this->personneID,
                                'attribut' => "Téléphone",
                                'avant' => $personne->telephone,
                                'apres' => $telephone,
                                'statut' => "attente",
                                'commentaire' => $commentaire
                            ];
                            $result = $this->modificationModel->insertModification($insert);
                            if (!$result) {
                                $data['errors'][] = ['Modification du téléphone' => "La modification du téléphone n’a pas pu aboutir. Veuillez réessayer."];
                            }
                        } else if ($personne->telephone !== $telephone && !empty($modification)) {
                            $update = [
                                'apres' => $telephone,
                                'commentaire' => $commentaire
                            ];
                            $result = $this->modificationModel->updateModification($modification->id_modification, $update);
                            if (!$result) {
                                $data['errors'][] = ['Modification du téléphone' => "La modification du téléphone n’a pas pu aboutir. Veuillez réessayer."];
                            }
                        } else if ($personne->telephone == $telephone && !empty($modification)) {
                            $this->modificationModel->deleteModification($modification->id_modification);
                        }
                    }
                    if ($this->request->getGetPost('bureau')) {
                        $bureau = intval($this->request->getGetPost('bureau'));
                        $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Bureau');
                        if ($bureauPersonne->id_bureau !== $bureau && empty($modification)) {
                            $insert = [
                                'id_personne' => $this->personneID,
                                'attribut' => "Bureau",
                                'avant' => $bureauPersonne->id_bureau,
                                'apres' => $bureau,
                                'statut' => "attente",
                                'commentaire' => $commentaire
                            ];
                            $result = $this->modificationModel->insertModification($insert);
                            if (!$result) {
                                $data['errors'][] = ['Modification du bureau' => "La modification du bureau n’a pas pu aboutir. Veuillez réessayer."];
                            }
                        } else if ($bureauPersonne->id_bureau !== $bureau && !empty($modification)) {
                            $update = [
                                'apres' => $bureau,
                                'commentaire' => $commentaire
                            ];
                            $result = $this->modificationModel->updateModification($modification->id_modification, $update);
                            if (!$result) {
                                $data['errors'][] = ['Modification du bureau' => "La modification du bureau n’a pas pu aboutir. Veuillez réessayer."];
                            }
                        } else if ($bureauPersonne->id_bureau == $bureau && !empty($modification)) {
                            $this->modificationModel->deleteModification($modification->id_modification);
                        }
                    }
                    if ($this->request->getGetPost('statut')) {
                        $statut = intval($this->request->getGetPost('statut'));
                        $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Statut');
                        if ($statutPersonne->id_statut !== $statut && empty($modification)) {
                            $insert = [
                                'id_personne' => $this->personneID,
                                'attribut' => "Statut",
                                'avant' => $statutPersonne->id_statut,
                                'apres' => $statut,
                                'statut' => "attente",
                                'commentaire' => $commentaire
                            ];
                            $result = $this->modificationModel->insertModification($insert);
                            if (!$result) {
                                $data['errors'][] = ['Modification du statut' => "La modification du statut n’a pas pu aboutir. Veuillez réessayer."];
                            }
                        } else if ($statutPersonne->id_statut !== $statut && !empty($modification)) {
                            $update = [
                                'apres' => $statut,
                                'commentaire' => $commentaire
                            ];
                            $result = $this->modificationModel->updateModification($modification->id_modification, $update);
                            if (!$result) {
                                $data['errors'][] = ['Modification du statut' => "La modification du statut n’a pas pu aboutir. Veuillez réessayer."];
                            }
                        } else if ($statutPersonne->id_statut == $statut && !empty($modification)) {
                            $this->modificationModel->deleteModification($modification->id_modification);
                        }
                    }
                    if ($this->request->getGetPost('equipe')) {
                        $equipesAPRES = $this->request->getGetPost('equipe');
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

                        $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Equipe');
                        if ($equipesAPRES != $equipesAVANT && empty($modification)) {
                            $listEquipeAVANT = implode(', ', $equipesAVANT);
                            $insert = [
                                'id_personne' => $this->personneID,
                                'attribut' => "Equipe",
                                'avant' => $listEquipeAVANT,
                                'apres' => $listEquipeAPRES,
                                'statut' => "attente",
                                'commentaire' => $commentaire
                            ];
                            $result = $this->modificationModel->insertModification($insert);
                            if (!$result) {
                                $data['errors'][] = ['Modification de l\'équipe' => "La modification de l’équipe n’a pas pu aboutir. Veuillez réessayer."];
                            }
                        } else if ($equipesAPRES != $equipesAVANT && !empty($modification)) {
                            $update = [
                                'apres' => $listEquipeAPRES,
                                'commentaire' => $commentaire
                            ];
                            $result = $this->modificationModel->updateModification($modification->id_modification, $update);
                            if (!$result) {
                                $data['errors'][] = ['Modification de l\'équipe' => "La modification de l'équipe n’a pas pu aboutir. Veuillez réessayer."];
                            }
                        } else if ($equipesAPRES == $equipesAVANT && !empty($modification)) {
                            $this->modificationModel->deleteModification($modification->id_modification);
                        }
                    }
                    if ($this->request->getGetPost('employeur')) {
                        $employeursAPRES = $this->request->getGetPost('employeur');
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
                        $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Employeur');
                        if ($employeursAPRES != $employeursAVANT && empty($modification)) {
                            $listEmployeurAVANT = implode(', ', $employeursAVANT);
                            $insert = [
                                'id_personne' => $this->personneID,
                                'attribut' => "Employeur",
                                'avant' => $listEmployeurAVANT,
                                'apres' => $listEmployeurAPRES,
                                'statut' => "attente",
                                'commentaire' => $commentaire
                            ];
                            $result = $this->modificationModel->insertModification($insert);
                            if (!$result) {
                                $data['errors'][] = ["Modification de l’employeur" => "La modification de l’employeur n’a pas pu aboutir. Veuillez réessayer."];
                            }
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
                        $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Activité');
                        if ($sejourPersonne->sujet !== $activite && empty($modification)) {
                            $insert = [
                                'id_personne' => $this->personneID,
                                'attribut' => "Activité",
                                'avant' => $sejourPersonne->sujet,
                                'apres' => $activite,
                                'statut' => "attente",
                                'commentaire' => $commentaire
                            ];
                            $result = $this->modificationModel->insertModification($insert);
                            if (!$result) {
                                $data['errors'][] = ["Modification de l’activité" => "La modification de l’activité n’a pas pu aboutir. Veuillez réessayer."];
                            }
                        } else if ($sejourPersonne->sujet !== $activite && !empty($modification)) {
                            $update = [
                                'apres' => $activite,
                                'commentaire' => $commentaire
                            ];
                            $result = $this->modificationModel->updateModification($modification->id_modification, $update);
                            if (!$result) {
                                $data['errors'][] = ["Modification de l’activité" => "La modification de l’activité n’a pas pu aboutir. Veuillez réessayer."];
                            }
                        } else if ($sejourPersonne->sujet == $activite && !empty($modification)) {
                            $this->modificationModel->deleteModification($modification->id_modification);
                        }
                    }
                }

                $allEmployeurs = $this->employeurModel->getAllEmployeurs();
                $allBureaux = $this->bureauModel->getAllBureaux();
                $allStatuts = $this->statutModel->getAllStatuts();
                $allEquipes = $this->equipeModel->getAllEquipes();

                // Données des modifications en attente si existantes
                $nomModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Nom');
                $prenomModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Prénom');
                $mailModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Mail');
                $telephoneModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Téléphone');
                $bureauModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Bureau');
                $statutModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Statut');
                $activiteModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Activité');
                $photoModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Photo');


                $equipesIDModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Equipe');
                // Convertir la liste des id des équipes en int
                $equipesModif = [];
                if (!empty($equipesIDModif)) {
                    $equipesID = explode(', ', $equipesIDModif->apres);
                    foreach ($equipesID as $equipeID) {
                        $equipesModif[] = intval($equipeID);
                    }
                }

                $employeursIDModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Employeur');
                // Convertir la liste des id des employeurs en int
                $employeursModif = [];
                if (!empty($employeursIDModif)) {
                    $employeursID = explode(', ', $employeursIDModif->apres);
                    foreach ($employeursID as $employeurID) {
                        $employeursModif[] = intval($employeurID);
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

                if (!empty($photoModif)) {
                    $data['photoModif'] = $photoModif;
                }

                return view('frontoffice/profile_edit', $data);
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