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
        $this->bureauModel = new BureauModel();
        $this->equipeModel = new EquipeModel();
        $this->modificationModel = new ModificationModel();
        $this->localisationModel = new LocalisationModel();

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

        // Récupération de l’utilisateur connecté
        $user = $this->session->get('user');
        if ($user) {
            $personneConnectee = $this->personneModel->getPersonneLogin($user['login']);
            if ($personneConnectee) {
                // Ajout de l’utilisateur connecté aux données
                $data['personneConnectee'] = $personneConnectee;
            }
        }

        // Stockage de l’ID de la personne dont on veut afficher le profil
        $this->personneID = $id;

        $personne = $this->personneModel->getPersonne($this->personneID);

        // Redirection vers la page d’accueil si la personne n’existe pas
        if (!$personne) {
            return redirect('/');
        }

        // Récupération des différentes entités liées à la personne
        $mails = $this->mailModel->getMailPersonne($this->personneID);
        $employeurs = $this->employeurModel->getEmployeurPersonne($this->personneID);
        $sejour = $this->sejourModel->getSejourPersonne($this->personneID);
        $responsabilites = $this->responsabiliteModel->getResponsabilitePersonne($this->personneID);
        $statut = $this->statutModel->getStatutPersonne($this->personneID);
        $equipes = $this->equipeModel->getEquipePersonne($this->personneID);
        $encadres = $this->personneModel->getEncadrePersonne($this->personneID);
        $bureaux = $this->bureauModel->getBureauxPersonne($this->personneID);
        $localisations = $this->localisationModel->getLocalisationsPersonne($this->personneID);

        // Ajout des entités récupérées aux données à passer à la vue
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
        if (!empty($bureaux)) {
            $data['bureaux'] = $bureaux;
        }
        if (!empty($localisations)) {
            $data['localisations'] = $localisations;
        }
        if (!empty($sejour) && !empty($personne)) {
            $data['responsables'] = $this->personneModel->getResponsablePersonne($personne->id_personne, $sejour->id_sejour);
        }

        return view('frontoffice/profile', $data);
    }

    /**
     * Méthode pour supprimer la photo de profil
     * @return RedirectResponse|string
     */
    public function deletePhoto()
    {
        // Récupération de l’utilisateur connecté
        $user = $this->session->get('user');
        if ($user) {
            $personneConnectee = $this->personneModel->getPersonneLogin($user['login']);
            if ($personneConnectee) {

                // Stockage de l’ID de la personne connectée
                $this->personneID = $personneConnectee->id_personne;
                $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'photo');

                // Suppression de la modification en attente si cette dernière existe
                if ($modification) {
                    $this->modificationModel->deleteModification($modification->id_modification);
                }

                // Retour à l’édition du profil
                return $this->edit();
            }
        }

        // Redirection vers la page d’accueil si l’utilisateur n’est pas connecté
        return redirect('/');
    }

    public function edit()
    {
        $data = [];

        $user = $this->session->get('user');
        if ($user) {
            $personneConnectee = $this->personneModel->getPersonneLogin($user['login']);
            if ($personneConnectee) {

                // Ajout de l’utilisateur connecté aux données
                $data['personneConnectee'] = $personneConnectee;

                // Stockage de l’ID de la personne connectée
                $this->personneID = $personneConnectee->id_personne;

                // Récupération des informations de la personne
                $personne = $this->personneModel->getPersonne($this->personneID);
                $mailPersonne = $this->mailModel->getMailPersonne($this->personneID);
                $employeursPersonne = $this->employeurModel->getEmployeurPersonne($this->personneID);
                $sejourPersonne = $this->sejourModel->getSejourPersonne($this->personneID);
                $responsabilitesPersonne = $this->responsabiliteModel->getResponsabilitePersonne($this->personneID);
                $statutPersonne = $this->statutModel->getStatutPersonne($this->personneID);
                $equipePersonne = $this->equipeModel->getEquipePersonne($this->personneID);
                $encadresPersonne = $this->personneModel->getEncadrePersonne($this->personneID);
                $bureauPersonne = $this->bureauModel->getBureauxPersonne($this->personneID);
                $localisationsPersonne = $this->localisationModel->getLocalisationsPersonne($this->personneID);

                // Vérification si le formulaire a été soumis
                if ($this->request->getGetPost()) {
                    // Traitement des modifications sur le profil
                    $commentaire = "";
                    if ($this->request->getGetPost('commentaire')) {
                        $commentaire = $this->request->getGetPost('commentaire');
                    }

                    // Règles de validation pour la photo de profil
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

                    // Récupération du fichier image soumis
                    $image = $this->request->getFile('photo_profile');

                    // Vérification et traitement de l’image soumise
                    if (!empty($image->getFileInfo()->getFilename())) {
                        if (!$this->validateData([], $validationRule)) {
                            // Ajout d’erreur de validation aux données
                            $data['errors'][] = ['Erreur de modification de photo' => $this->validator->getError('photo_profile')];
                        } else if (!$image->isValid()) {
                            // Ajout d’erreur d’upload aux données
                            $data['errors'][] = "Erreur dans l’upload du fichier. Veuillez vérifier votre fichier et essayer ultérieurement.";
                        } else if (!$image->hasMoved()) {
                            // Déplacement du fichier vers le dossier de stockage
                            $result = $image->move('assets/images/profile/en_attente',
                                $this->personneID . '.jpg', true);
                            if ($result) {
                                // Ajout de succès aux données
                                $data['success'][] = ["Modification de la photo de profile" => "Ajout de la photo avec succès !"];

                                // Vérification si une modification de la photo est en attente
                                $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Photo');
                                $imgUrl = img_url('');
                                if (empty($modification)) {
                                    // Insertion d'une nouvelle modification
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
                                    // Mise à jour de la modification existante
                                    $update = [
                                        'apres' => $imgUrl . 'profile/en_attente/' . $this->personneID,
                                        'commentaire' => $commentaire
                                    ];
                                    $this->modificationModel->updateModification($modification->id_modification, $update);
                                }
                            } else {
                                // Ajout d’erreur d’upload aux données
                                $data['errors'][] = "Echec de l’ajout de l’image. Veuillez vérifier votre fichier et essayer ultérieurement.";
                            }
                        } else {
                            // Ajout d’erreur d’upload aux données
                            $data['errors'][] = "Echec de l’ajout de l’image. Veuillez vérifier votre fichier et essayer ultérieurement.";
                        }
                    }

                    // Vérification si le champ 'nom' a été soumis dans la requête
                    if ($this->request->getGetPost('nom')) {
                        // Récupération de la nouvelle valeur du nom depuis la requête
                        $nom = $this->request->getGetPost('nom');

                        // Récupération de la modification en attente pour l’attribut 'Nom'
                        $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Nom');

                        // Vérification si le nom a changé et s'il n'y a de modification en attente
                        if ($personne->nom !== $nom && empty($modification)) {
                            // Création d'un tableau de données pour insérer une nouvelle modification
                            $insert = [
                                'id_personne' => $this->personneID,
                                'attribut' => "Nom",
                                'avant' => $personne->nom,
                                'apres' => $nom,
                                'statut' => "attente",
                                'commentaire' => $commentaire
                            ];
                            // Insertion de la nouvelle modification dans la base de données
                            $result = $this->modificationModel->insertModification($insert);

                            // Vérification si l'insertion a échoué
                            if (!$result) {
                                // Ajout d'un message d'erreur dans le tableau des erreurs
                                $data['errors'][] = ['Modification du nom' => "La modification du nom n’a pas pu aboutir. Veuillez réessayer."];
                            }
                            // Si le nom a changé et qu'il y a déjà une modification en attente
                        } else if ($personne->nom !== $nom && !empty($modification)) {
                            // Mise à jour de la modification en attente avec la nouvelle valeur
                            $update = [
                                'apres' => $nom,
                                'commentaire' => $commentaire
                            ];
                            // Mise à jour de la modification dans la base de données
                            $result = $this->modificationModel->updateModification($modification->id_modification, $update);

                            // Vérification si la mise à jour a échoué
                            if (!$result) {
                                // Ajout d'un message d'erreur dans le tableau des erreurs
                                $data['errors'][] = ['Modification du nom' => "La modification du nom n’a pas pu aboutir. Veuillez réessayer."];
                            }
                            // Si le nom est identique et qu'il y a une modification en attente
                        } else if ($personne->nom == $nom && !empty($modification)) {
                            // Suppression de la modification en attente
                            $this->modificationModel->deleteModification($modification->id_modification);
                        }
                    }

                    // Vérification si le champ 'prenom' a été soumis dans la requête
                    if ($this->request->getGetPost('prenom')) {
                        // Récupération de la nouvelle valeur du prénom depuis la requête
                        $prenom = $this->request->getGetPost('prenom');

                        // Récupération de la modification en attente pour l'attribut 'Prénom'
                        $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Prénom');

                        // Vérification si le prénom a changé et s'il n'y a pas de modification en attente
                        if ($personne->prenom !== $prenom && empty($modification)) {
                            // Création d'un tableau de données pour insérer une nouvelle modification
                            $insert = [
                                'id_personne' => $this->personneID,
                                'attribut' => "Prénom",
                                'avant' => $personne->prenom,
                                'apres' => $prenom,
                                'statut' => "attente",
                                'commentaire' => $commentaire
                            ];
                            // Insertion de la nouvelle modification dans la base de données
                            $result = $this->modificationModel->insertModification($insert);

                            // Vérification si l'insertion a échoué
                            if (!$result) {
                                // Ajout d'un message d'erreur dans le tableau des erreurs
                                $data['errors'][] = ['Modification du prénom' => "La modification du prénom n’a pas pu aboutir. Veuillez réessayer."];
                            }
                            // Si le prénom a changé et qu'il y a déjà une modification en attente
                        } else if ($personne->prenom !== $prenom && !empty($modification)) {
                            // Mise à jour de la modification en attente avec la nouvelle valeur
                            $update = [
                                'apres' => $prenom,
                                'commentaire' => $commentaire
                            ];
                            // Mise à jour de la modification dans la base de données
                            $result = $this->modificationModel->updateModification($modification->id_modification, $update);

                            // Vérification si la mise à jour a échoué
                            if (!$result) {
                                // Ajout d'un message d'erreur dans le tableau des erreurs
                                $data['errors'][] = ['Modification du prénom' => "La modification du prénom n’a pas pu aboutir. Veuillez réessayer."];
                            }
                            // Si le prénom est identique et qu'il y a une modification en attente
                        } else if ($personne->prenom == $prenom && !empty($modification)) {
                            // Suppression de la modification en attente
                            $this->modificationModel->deleteModification($modification->id_modification);
                        }
                    }

                    // Vérification si le champ 'email' a été soumis dans la requête
                    if ($this->request->getGetPost('email')) {
                        // Récupération de la nouvelle valeur de l'email depuis la requête
                        $email = $this->request->getGetPost('email');
                        // Récupération de la modification en attente pour l'attribut 'Mail'
                        $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Mail');

                        // Vérification si l'email a changé et s'il n'y a pas de modification en attente
                        if ($mailPersonne->libelle !== $email && empty($modification)) {
                            // Création d'un tableau de données pour insérer une nouvelle modification
                            $insert = [
                                'id_personne' => $this->personneID,
                                'attribut' => "Mail",
                                'avant' => $mailPersonne->libelle,
                                'apres' => $email,
                                'statut' => "attente",
                                'commentaire' => $commentaire
                            ];
                            // Insertion de la nouvelle modification dans la base de données
                            $result = $this->modificationModel->insertModification($insert);

                            // Vérification si l'insertion a échoué
                            if (!$result) {
                                // Ajout d'un message d'erreur dans le tableau des erreurs
                                $data['errors'][] = ['Modification du mail' => "La modification du mail n’a pas pu aboutir. Veuillez réessayer."];
                            }
                            // Si l'email a changé et qu'il y a déjà une modification en attente
                        } else if ($mailPersonne->libelle !== $email && !empty($modification)) {
                            // Mise à jour de la modification en attente avec la nouvelle valeur
                            $update = [
                                'apres' => $email,
                                'commentaire' => $commentaire
                            ];
                            // Mise à jour de la modification dans la base de données
                            $result = $this->modificationModel->updateModification($modification->id_modification, $update);

                            // Vérification si la mise à jour a échoué
                            if (!$result) {
                                // Ajout d'un message d'erreur dans le tableau des erreurs
                                $data['errors'][] = ['Modification du mail' => "La modification du mail n’a pas pu aboutir. Veuillez réessayer."];
                            }
                            // Si l'email est identique et qu'il y a une modification en attente
                        } else if ($mailPersonne->libelle == $email && !empty($modification)) {
                            // Suppression de la modification en attente
                            $this->modificationModel->deleteModification($modification->id_modification);
                        }
                    }

                    // Vérification si le champ 'telephone' a été soumis dans la requête
                    if ($this->request->getGetPost('telephone')) {
                        // Récupérer les téléphones soumis et les convertir en tableau
                        $telephonesStr = $this->request->getGetPost('telephone');
                        $telephonesAPRES = explode(', ', $telephonesStr);

                        // Récupérer les téléphones actuels depuis la base de données
                        $telephonesAVANT = [];
                        foreach ($localisationsPersonne as $localisation) {
                            if (!empty($localisation->telephone)) {
                                $telephonesAVANT[] = $localisation->telephone;
                            }
                        }

                        // Trier les listes de téléphones pour comparaison
                        asort($telephonesAVANT);
                        asort($telephonesAPRES);

                        // Convertir les listes de téléphones en chaînes de caractères
                        $listTelephoneAPRES = implode(', ', $telephonesAPRES);
                        $listTelephoneAVANT = implode(', ', $telephonesAVANT);

                        // Récupérer la modification en attente pour l'attribut 'Téléphone'
                        $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Téléphone');

                        // Vérifier si les téléphones ont changé et s'il n'y a pas de modification en attente
                        if ($listTelephoneAPRES !== $listTelephoneAVANT && empty($modification)) {
                            $insert = [
                                'id_personne' => $this->personneID,
                                'attribut' => "Téléphone",
                                'avant' => $listTelephoneAVANT,
                                'apres' => $listTelephoneAPRES,
                                'statut' => "attente",
                                'commentaire' => $commentaire
                            ];
                            $result = $this->modificationModel->insertModification($insert);
                            if (!$result) {
                                $data['errors'][] = ['Modification du téléphone' => "La modification du téléphone n’a pas pu aboutir. Veuillez réessayer."];
                            }
                        } else if ($listTelephoneAPRES !== $listTelephoneAVANT && !empty($modification)) {
                            $update = [
                                'apres' => $listTelephoneAPRES,
                                'commentaire' => $commentaire
                            ];
                            $result = $this->modificationModel->updateModification($modification->id_modification, $update);
                            if (!$result) {
                                $data['errors'][] = ['Modification du téléphone' => "La modification du téléphone n’a pas pu aboutir. Veuillez réessayer."];
                            }
                        } else if ($listTelephoneAPRES === $listTelephoneAVANT && !empty($modification)) {
                            $this->modificationModel->deleteModification($modification->id_modification);
                        }
                    }

                    if ($this->request->getGetPost('bureau')) {
                        // Récupérer les nouvelles valeurs des bureaux depuis la requête
                        $bureauxAPRES = $this->request->getGetPost('bureau');
                        $bureauINT = [];
                        foreach ($bureauxAPRES as $bur) {
                            $bureauINT[] = intval($bur);
                        }
                        $bureauxAPRES = $bureauINT;

                        // Récupérer les valeurs actuelles des bureaux
                        $bureauxAVANT = [];
                        foreach ($localisationsPersonne as $localisation) {
                            if (!empty($localisation->bureau)) {
                                $bureauxAVANT[] = $localisation->bureau;
                            }
                        }

                        // Trier les listes de bureaux pour comparaison
                        asort($bureauxAVANT);
                        asort($bureauxAPRES);

                        // Convertir les listes de bureaux en chaînes de caractères
                        $listBureauAPRES = implode(', ', $bureauxAPRES);

                        // Récupérer la modification en attente pour l'attribut 'Bureau'
                        $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Bureau');

                        // Vérifier si les bureaux ont changé et s'il n'y a pas de modification en attente
                        if ($bureauxAPRES != $bureauxAVANT && empty($modification)) {
                            $listBureauAVANT = implode(', ', $bureauxAVANT);
                            $insert = [
                                'id_personne' => $this->personneID,
                                'attribut' => "Bureau",
                                'avant' => $listBureauAVANT,
                                'apres' => $listBureauAPRES,
                                'statut' => "attente",
                                'commentaire' => $commentaire
                            ];
                            $result = $this->modificationModel->insertModification($insert);
                            if (!$result) {
                                $data['errors'][] = ['Modification du bureau' => "La modification du bureau n’a pas pu aboutir. Veuillez réessayer."];
                            }
                        } else if ($bureauxAPRES != $bureauxAVANT && !empty($modification)) {
                            $update = [
                                'apres' => $listBureauAPRES,
                                'commentaire' => $commentaire
                            ];
                            $result = $this->modificationModel->updateModification($modification->id_modification, $update);
                            if (!$result) {
                                $data['errors'][] = ['Modification du bureau' => "La modification du bureau n’a pas pu aboutir. Veuillez réessayer."];
                            }
                        } else if ($bureauxAPRES == $bureauxAVANT && !empty($modification)) {
                            $this->modificationModel->deleteModification($modification->id_modification);
                        }
                    }

                    // Vérification si le champ 'statut' a été soumis dans la requête
                    if ($this->request->getGetPost('statut')) {
                        // Récupération de la nouvelle valeur du statut depuis la requête
                        $statut = intval($this->request->getGetPost('statut'));
                        // Récupération de la modification en attente pour l'attribut 'Statut'
                        $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Statut');

                        // Vérification si le statut a changé et s'il n'y a pas de modification en attente
                        if ($statutPersonne->id_statut !== $statut && empty($modification)) {
                            // Création d'un tableau de données pour insérer une nouvelle modification
                            $insert = [
                                'id_personne' => $this->personneID,
                                'attribut' => "Statut",
                                'avant' => $statutPersonne->id_statut,
                                'apres' => $statut,
                                'statut' => "attente",
                                'commentaire' => $commentaire
                            ];
                            // Insertion de la nouvelle modification dans la base de données
                            $result = $this->modificationModel->insertModification($insert);

                            // Vérification si l'insertion a échoué
                            if (!$result) {
                                // Ajout d'un message d'erreur dans le tableau des erreurs
                                $data['errors'][] = ['Modification du statut' => "La modification du statut n’a pas pu aboutir. Veuillez réessayer."];
                            }
                            // Si le statut a changé et qu'il y a déjà une modification en attente
                        } else if ($statutPersonne->id_statut !== $statut && !empty($modification)) {
                            // Mise à jour de la modification en attente avec la nouvelle valeur
                            $update = [
                                'apres' => $statut,
                                'commentaire' => $commentaire
                            ];
                            // Mise à jour de la modification dans la base de données
                            $result = $this->modificationModel->updateModification($modification->id_modification, $update);

                            // Vérification si la mise à jour a échoué
                            if (!$result) {
                                // Ajout d'un message d'erreur dans le tableau des erreurs
                                $data['errors'][] = ['Modification du statut' => "La modification du statut n’a pas pu aboutir. Veuillez réessayer."];
                            }
                            // Si le statut est identique et qu'il y a une modification en attente
                        } else if ($statutPersonne->id_statut == $statut && !empty($modification)) {
                            // Suppression de la modification en attente
                            $this->modificationModel->deleteModification($modification->id_modification);
                        }
                    }

                    // Vérification si le champ 'equipe' a été soumis dans la requête
                    if ($this->request->getGetPost('equipe')) {
                        // Récupération des nouvelles valeurs des équipes depuis la requête
                        $equipesAPRES = $this->request->getGetPost('equipe');
                        $equipeINT = [];
                        // Conversion des valeurs des équipes en entiers
                        foreach ($equipesAPRES as $eq) {
                            $equipeINT[] = intval($eq);
                        }
                        $equipesAPRES = $equipeINT;

                        // Récupération des valeurs actuelles des équipes
                        $equipesAVANT = [];
                        foreach ($equipePersonne as $equipeP) {
                            $equipesAVANT[] = $equipeP->id_equipe;
                        }

                        // Tri des listes d'équipes pour comparaison
                        asort($equipesAVANT);
                        asort($equipesAPRES);

                        // Conversion des listes d'équipes en chaînes de caractères
                        $listEquipeAPRES = implode(', ', $equipesAPRES);

                        // Récupération de la modification en attente pour l'attribut 'Equipe'
                        $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Equipe');

                        // Vérification si les équipes ont changé et s'il n'y a pas de modification en attente
                        if ($equipesAPRES != $equipesAVANT && empty($modification)) {
                            $listEquipeAVANT = implode(', ', $equipesAVANT);
                            // Création d'un tableau de données pour insérer une nouvelle modification
                            $insert = [
                                'id_personne' => $this->personneID,
                                'attribut' => "Equipe",
                                'avant' => $listEquipeAVANT,
                                'apres' => $listEquipeAPRES,
                                'statut' => "attente",
                                'commentaire' => $commentaire
                            ];
                            // Insertion de la nouvelle modification dans la base de données
                            $result = $this->modificationModel->insertModification($insert);

                            // Vérification si l'insertion a échoué
                            if (!$result) {
                                // Ajout d'un message d'erreur dans le tableau des erreurs
                                $data['errors'][] = ['Modification de l\'équipe' => "La modification de l’équipe n’a pas pu aboutir. Veuillez réessayer."];
                            }
                            // Si les équipes ont changé et qu'il y a déjà une modification en attente
                        } else if ($equipesAPRES != $equipesAVANT && !empty($modification)) {
                            // Mise à jour de la modification en attente avec la nouvelle valeur
                            $update = [
                                'apres' => $listEquipeAPRES,
                                'commentaire' => $commentaire
                            ];
                            // Mise à jour de la modification dans la base de données
                            $result = $this->modificationModel->updateModification($modification->id_modification, $update);

                            // Vérification si la mise à jour a échoué
                            if (!$result) {
                                // Ajout d'un message d'erreur dans le tableau des erreurs
                                $data['errors'][] = ['Modification de l\'équipe' => "La modification de l'équipe n’a pas pu aboutir. Veuillez réessayer."];
                            }
                            // Si les équipes sont identiques et qu'il y a une modification en attente
                        } else if ($equipesAPRES == $equipesAVANT && !empty($modification)) {
                            // Suppression de la modification en attente
                            $this->modificationModel->deleteModification($modification->id_modification);
                        }
                    }

                    // Vérification si le champ 'employeur' a été soumis dans la requête
                    if ($this->request->getGetPost('employeur')) {
                        // Récupération des nouvelles valeurs des employeurs depuis la requête
                        $employeursAPRES = $this->request->getGetPost('employeur');
                        $employeurINT = [];
                        // Conversion des valeurs des employeurs en entiers
                        foreach ($employeursAPRES as $employeur) {
                            $employeurINT[] = intval($employeur);
                        }
                        $employeursAPRES = $employeurINT;

                        // Récupération des valeurs actuelles des employeurs
                        $employeursAVANT = [];
                        foreach ($employeursPersonne as $employeur) {
                            $employeursAVANT[] = $employeur->id_employeur;
                        }

                        // Tri des listes d'employeurs pour comparaison
                        asort($employeursAVANT);
                        asort($employeursAPRES);
                        // Conversion des listes d'employeurs en chaînes de caractères
                        $listEmployeurAPRES = implode(', ', $employeursAPRES);

                        // Récupération de la modification en attente pour l'attribut 'Employeur'
                        $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Employeur');

                        // Vérification si les employeurs ont changé et s'il n'y a pas de modification en attente
                        if ($employeursAPRES != $employeursAVANT && empty($modification)) {
                            $listEmployeurAVANT = implode(', ', $employeursAVANT);
                            // Création d'un tableau de données pour insérer une nouvelle modification
                            $insert = [
                                'id_personne' => $this->personneID,
                                'attribut' => "Employeur",
                                'avant' => $listEmployeurAVANT,
                                'apres' => $listEmployeurAPRES,
                                'statut' => "attente",
                                'commentaire' => $commentaire
                            ];
                            // Insertion de la nouvelle modification dans la base de données
                            $result = $this->modificationModel->insertModification($insert);

                            // Vérification si l'insertion a échoué
                            if (!$result) {
                                // Ajout d'un message d'erreur dans le tableau des erreurs
                                $data['errors'][] = ["Modification de l’employeur" => "La modification de l’employeur n’a pas pu aboutir. Veuillez réessayer."];
                            }
                            // Si les employeurs ont changé et qu'il y a déjà une modification en attente
                        } else if ($employeursAPRES != $employeursAVANT && !empty($modification)) {
                            // Mise à jour de la modification en attente avec la nouvelle valeur
                            $update = [
                                'apres' => $listEmployeurAPRES,
                                'commentaire' => $commentaire
                            ];
                            // Mise à jour de la modification dans la base de données
                            $this->modificationModel->updateModification($modification->id_modification, $update);
                            // Si les employeurs sont identiques et qu'il y a une modification en attente
                        } else if ($employeursAPRES == $employeursAVANT && !empty($modification)) {
                            // Suppression de la modification en attente
                            $this->modificationModel->deleteModification($modification->id_modification);
                        }
                    }

                    // Vérification si le champ 'activite' a été soumis dans la requête
                    if ($this->request->getGetPost('activite')) {
                        // Récupération de la nouvelle valeur de l'activité depuis la requête
                        $activite = $this->request->getGetPost('activite');
                        // Récupération de la modification en attente pour l'attribut 'Activité'
                        $modification = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Activité');

                        // Vérification si l'activité a changé et s'il n'y a pas de modification en attente
                        if ($sejourPersonne->sujet !== $activite && empty($modification)) {
                            // Création d'un tableau de données pour insérer une nouvelle modification
                            $insert = [
                                'id_personne' => $this->personneID,
                                'attribut' => "Activité",
                                'avant' => $sejourPersonne->sujet,
                                'apres' => $activite,
                                'statut' => "attente",
                                'commentaire' => $commentaire
                            ];
                            // Insertion de la nouvelle modification dans la base de données
                            $result = $this->modificationModel->insertModification($insert);

                            // Vérification si l'insertion a échoué
                            if (!$result) {
                                // Ajout d'un message d'erreur dans le tableau des erreurs
                                $data['errors'][] = ["Modification de l’activité" => "La modification de l’activité n’a pas pu aboutir. Veuillez réessayer."];
                            }
                            // Si l'activité a changé et qu'il y a déjà une modification en attente
                        } else if ($sejourPersonne->sujet !== $activite && !empty($modification)) {
                            // Mise à jour de la modification en attente avec la nouvelle valeur
                            $update = [
                                'apres' => $activite,
                                'commentaire' => $commentaire
                            ];
                            // Mise à jour de la modification dans la base de données
                            $result = $this->modificationModel->updateModification($modification->id_modification, $update);

                            // Vérification si la mise à jour a échoué
                            if (!$result) {
                                // Ajout d'un message d'erreur dans le tableau des erreurs
                                $data['errors'][] = ["Modification de l’activité" => "La modification de l’activité n’a pas pu aboutir. Veuillez réessayer."];
                            }
                            // Si l'activité est identique et qu'il y a une modification en attente
                        } else if ($sejourPersonne->sujet == $activite && !empty($modification)) {
                            // Suppression de la modification en attente
                            $this->modificationModel->deleteModification($modification->id_modification);
                        }
                    }

                }

                // Récupération de toutes les entités employeurs, bureaux, statuts et équipes
                $allEmployeurs = $this->employeurModel->getAllEmployeurs();
                $allBureaux = $this->bureauModel->getAllBureaux();
                $allStatuts = $this->statutModel->getAllStatuts();
                $allEquipes = $this->equipeModel->getAllEquipes();

                // Récupération des modifications en attente pour les différents attributs de la personne
                $nomModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Nom');
                $prenomModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Prénom');
                $mailModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Mail');
                $telephoneModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Téléphone');
                $bureauModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Bureau');
                $statutModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Statut');
                $activiteModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Activité');
                $photoModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Photo');


                // Récupération et conversion des ID des équipes modifiées en attente en une liste d'entiers
                $equipesIDModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Equipe');
                $equipesModif = [];
                if (!empty($equipesIDModif)) {
                    $equipesID = explode(', ', $equipesIDModif->apres);
                    foreach ($equipesID as $equipeID) {
                        $equipesModif[] = intval($equipeID);
                    }
                }

                // Récupération et conversion des ID des employeurs modifiés en attente en une liste d'entiers
                $employeursIDModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Employeur');
                $employeursModif = [];
                if (!empty($employeursIDModif)) {
                    $employeursID = explode(', ', $employeursIDModif->apres);
                    foreach ($employeursID as $employeurID) {
                        $employeursModif[] = intval($employeurID);
                    }
                }

                // Récupération et conversion des téléphones modifiés en attente en une liste d'entiers
                $telephonesModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Téléphone');

                // Récupération et conversion des bureaux modifiés en attente en une liste d'entiers
                $bureauxIDModif = $this->modificationModel->getModificationAttentePersonneAttribut($this->personneID, 'Bureau');
                $bureauxModif = [];
                if (!empty($bureauxIDModif)) {
                    $bureauxID = explode(', ', $bureauxIDModif->apres);
                    foreach ($bureauxID as $bureauID) {
                        $bureauxModif[] = intval($bureauID);
                    }
                }

                // Ajout des informations de la personne et des modifications en attente aux données à envoyer à la vue
                if (!empty($personne)) {
                    $data['personne'] = $personne;
                }

                if (!empty($statutPersonne)) {
                    $data['statutPersonne'] = $statutPersonne;
                }

                if (!empty($localisationsPersonne)) {
                    $data['localisationsPersonne'] = $localisationsPersonne;
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
                    $data['telephoneModif'] = $telephonesModif;
                }

                if (!empty($bureauModif)) {
                    $data['bureauModif'] = $bureauxModif;
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