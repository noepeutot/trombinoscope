<?php

namespace App\Controllers;

use App\Models\BureauModel;
use App\Models\EmployeurModel;
use App\Models\EquipeModel;
use App\Models\FinancementModel;
use App\Models\MailModel;
use App\Models\ModificationModel;
use App\Models\PersonneModel;
use App\Models\SejourModel;
use App\Models\StatutModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Session\Session;
use Config\Services;
use Psr\Log\LoggerInterface;

class ModerationAdmin extends BaseController
{
    protected Session $session;
    protected PersonneModel $personneModel;
    protected ModificationModel $modificationModel;
    protected MailModel $mailModel;
    protected EmployeurModel $employeurModel;
    protected FinancementModel $financementModel;
    protected StatutModel $statutModel;
    protected BureauModel $bureauModel;
    protected EquipeModel $equipeModel;
    protected SejourModel $sejourModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->session = Services::session();
        $this->personneModel = new PersonneModel();
        $this->modificationModel = new ModificationModel();

        $this->mailModel = new MailModel();
        $this->employeurModel = new EmployeurModel();
        $this->financementModel = new FinancementModel();
        $this->statutModel = new StatutModel();
        $this->bureauModel = new BureauModel();
        $this->equipeModel = new EquipeModel();
        $this->sejourModel = new SejourModel();
    }

    /**
     * Méthode pour afficher la page principale de modération
     * @return RedirectResponse|string
     */
    public function index()
    {
        // Récupération de l’utilisateur connecté depuis la session
        $user = $this->session->get('user');

        // Vérification si un utilisateur est connecté et s’il a les rôles requis (admin ou modo)
        if ($user) {
            $personneConnectee = $this->personneModel->getPersonneLogin($user['login']);
            if ($personneConnectee && ($personneConnectee->role === 'admin' || $personneConnectee->role === 'modo')) {
                // Redirection en fonction de l’URI demandé
                switch (uri_string()) {
                    case 'backoffice/moderation':
                        return $this->moderation();

                    case 'backoffice/moderation/en-attente':
                        return $this->attente();

                    case 'backoffice/moderation/historique':
                        return $this->historique();
                }
            }
        }

        // Redirection vers la page d’accueil si l’utilisateur n’est pas autorisé
        return redirect('/');
    }

    public function moderation(): string
    {
        $data = [];

        // Récupération des modifications récentes en attente et historiques avec leur nombre total
        $modificationEnAttente = $this->modificationModel->getModificationEnAttenteRecente(4);
        $ModificationHistorique = $this->modificationModel->getModificationHistoriqueRecente(4);
        $nombreEnAttente = $this->modificationModel->countModificationEnAttente();
        $nombreHistorique = $this->modificationModel->countModificationHistorique();

        // Enrichissement des modifications avec les entités des éléments
        if (!empty($modificationEnAttente)) {
            $this->processModifications($modificationEnAttente);
        }
        if (!empty($ModificationHistorique)) {
            $this->processModifications($ModificationHistorique);
        }

        $data['modificationEnAttente'] = $modificationEnAttente;
        $data['modificationHistorique'] = $ModificationHistorique;
        $data['nombreEnAttente'] = $nombreEnAttente;
        $data['nombreHistorique'] = $nombreHistorique;
        $data['activePage'] = 'moderation';
        return view('backoffice/moderation', $data);
    }

    /**
     * Méthode pour traiter une liste de modifications et les enrichir
     * @param $modifications
     * @return void
     */
    public function processModifications($modifications)
    {
        foreach ($modifications as $modification) {
            $this->enrichModification($modification);
        }
    }

    /**
     * Méthode pour enrichir une modification avec des entités complètes
     * @param $modification
     * @return void
     */
    public function enrichModification($modification)
    {
        // Récupération des informations de la personne liée à la modification
        $personne = $this->personneModel->getPersonne($modification->id_personne);
        $modification->nom = $personne->nom;
        $modification->prenom = $personne->prenom;

        // Enrichissement de la modification en fonction de l’attribut modifié
        switch ($modification->attribut) {
            case "Bureau":
                $this->enrichBureau($modification);
                break;
            case "Statut":
                $this->enrichStatut($modification);
                break;
            case "Equipe":
                $this->enrichEquipe($modification);
                break;
            case "Employeur":
                $this->enrichEmployeur($modification);
                break;
        }
    }

    /**
     * Attribution des entités bureau dans la modification
     * @param $modification
     * @return void
     */
    private function enrichBureau($modification)
    {
        // Récupération des entités bureau avant et après de la modification
        $bureauAvant = $this->bureauModel->getBureau(intval($modification->avant));
        $bureauApres = $this->bureauModel->getBureau(intval($modification->apres));

        // Stockage des entités statut d’avant et d’après la modification
        $modification->bureauAvant = $bureauAvant;
        $modification->bureauApres = $bureauApres;
    }

    /**
     * Attribution des entités statut dans la modification
     * @param $modification
     * @return void
     */
    private function enrichStatut($modification)
    {
        // Récupération des entités statut avant et après de la modification
        $statutAvant = $this->statutModel->getStatut(intval($modification->avant));
        $statutApres = $this->statutModel->getStatut(intval($modification->apres));

        // Stockage des entités dans la modification
        $modification->statutAvant = $statutAvant;
        $modification->statutApres = $statutApres;
    }

    /**
     * Attribution des entités équipe dans la modification
     * @param $modification
     * @return void
     */
    private function enrichEquipe($modification)
    {
        $equipesAvant = [];
        $equipesApres = [];

        // Récupération des ID des équipes avant la modification (séparés par des virgules)
        $equipesIDAvant = explode(', ', $modification->avant);
        foreach ($equipesIDAvant as $equipeID) {
            // Conversion de l’ID en entier et récupération de l’entité équipe correspondante
            $equipesAvant[] = $this->equipeModel->getEquipe(intval($equipeID));
        }

        // Récupération des ID des équipes après la modification (séparés par des virgules)
        $equipesIDApres = explode(', ', $modification->apres);
        foreach ($equipesIDApres as $equipeID) {
            // Conversion de l’ID en entier et récupération de l’entité équipe correspondante
            $equipesApres[] = $this->equipeModel->getEquipe(intval($equipeID));
        }

        // Stockage des entités équipes avant et après dans la modification
        $modification->equipeAvant = $equipesAvant;
        $modification->equipeApres = $equipesApres;
    }

    /**
     * Attribution des entités employeur dans la modification
     * @param $modification
     * @return void
     */
    private function enrichEmployeur($modification)
    {
        $employeurAvant = [];
        $employeurApres = [];

        // Récupération des ID des employeurs avant la modification (séparés par des virgules)
        $employeursIDAvant = explode(', ', $modification->avant);
        foreach ($employeursIDAvant as $employeurID) {
            // Conversion de l’ID en entier et récupération de l’entité employeur correspondante
            $employeurAvant[] = $this->employeurModel->getEmployeur(intval($employeurID));
        }

        // Récupération des ID des employeurs après la modification (séparés par des virgules)
        $employeursIDApres = explode(', ', $modification->apres);
        foreach ($employeursIDApres as $employeurID) {
            // Conversion de l’ID en entier et récupération de l’entité employeur correspondante
            $employeurApres[] = $this->employeurModel->getEmployeur(intval($employeurID));
        }

        // Stockage des entités employeurs avant et après dans la modification
        $modification->employeurAvant = $employeurAvant;
        $modification->employeurApres = $employeurApres;
    }

    /**
     * Fonction d’affichage de la page des modifications en attente
     * @return string
     */
    public function attente(): string
    {
        $data = [];

        if ($this->request->getPost()) {
            // Traitement de l’annulation d’une modification
            if ($this->request->getPost('annule')) {

                // Récupération de l’ID de la modification à annuler à partir des données POST
                $id_modification = $this->request->getPost('annule');

                // Récupération des détails de la modification à annuler
                $modification = $this->modificationModel->getModification($id_modification);

                if ($modification) {
                    // Récupération de l’attribut modifié et de la personne ayant fait la modification
                    $attribut = $modification->attribut;
                    $personne = $this->personneModel->getPersonne($modification->id_personne);
                    if ($personne) {
                        // Récupération de l’adresse email de la personne
                        $mail = $this->mailModel->getMailPersonne($personne->id_personne);

                        // Envoi d’un mail de refus à la personne
                        $this->sendMail($mail->libelle, $attribut, $personne, 'templates/email-refuse', false);

                        // Mise à jour du statut de la modification à "annule" dans la base de données
                        $this->modificationModel->updateModification($id_modification, ['statut' => 'annule']);
                    }
                }
                // Traitement de la validation d’une modification
            } elseif ($this->request->getPost('valide')) {

                // Récupération de l'ID de la modification à valider à partir des données POST
                $id_modification = $this->request->getPost('valide');

                // Récupération des détails de la modification à valider
                $modification = $this->modificationModel->getModification($id_modification);

                if ($modification) {
                    // Récupération de l’attribut modifié et de la personne ayant fait la modification
                    $attribut = $modification->attribut;
                    $personne = $this->personneModel->getPersonne($modification->id_personne);
                    if ($personne) {
                        // Récupération de l’adresse email de la personne
                        $mail = $this->mailModel->getMailPersonne($personne->id_personne);

                        // Envoi d’un mail de validation à la personne
                        $this->sendMail($mail->libelle, $attribut, $personne, 'templates/email-valide', true);

                        // Mise à jour du statut de la modification à "valide" dans la base de données
                        $this->modificationModel->updateModification($id_modification, ['statut' => 'valide']);
                    }
                }
            }
        }

        // Récupération de toutes les modifications en attente et du nombre
        $modificationEnAttente = $this->modificationModel->getModificationEnAttenteRecente(-1);
        $nombreEnAttente = $this->modificationModel->countModificationEnAttente();
        if (!empty($modificationEnAttente)) {
            // Enrichissement des modifications en attente avec les entités complètes
            $this->processModifications($modificationEnAttente);
        }

        $data['modificationEnAttente'] = $modificationEnAttente;
        $data['nombreEnAttente'] = $nombreEnAttente;
        $data['activePage'] = 'moderation';
        return view('backoffice/en-attente', $data);
    }

    /**
     * Fonction qui envoie un mail
     * @param string $destinataire du mail
     * @param string $attribut qui fait l’objet de modification
     * @param array|object $personne fait la modification
     * @param string $view template du mail
     * @param bool $validation true si validé et false sinon
     * @return boolean
     */
    public function sendMail(string $destinataire, string $attribut, $personne, string $view, bool $validation): bool
    {
        $email = Services::email();

        // Définition de l’adresse email du destinataire
        $email->setTo($destinataire);

        // Définition du sujet de l’email en fonction de la validation
        $subject = $validation ? 'Validation modification' : 'Refus modification';
        $email->setSubject($subject);

        // Préparation des données à inclure dans le template de l'email
        $data['attribut'] = $attribut;
        $data['nom'] = $personne->nom;
        $data['prenom'] = $personne->prenom;

        // Rendu du template de l’email avec les données préparées
        $template = view($view, $data);

        // Définition du message et envoie du mail
        $email->setMessage($template);
        $response = $email->send();

        // Enregistrement dans les logs l’envoie avec succès ou échec
        $response ? log_message("error", "Email has been sent") : log_message("error", $email->printDebugger());
        return $response;
    }

    /**
     * Méthode pour afficher la page de l'historique des modifications
     * @return string
     */
    public function historique(): string
    {
        $data = [];

        // Récupération de toutes les modifications historiques récentes et le nombre total
        $modificationHistorique = $this->modificationModel->getModificationHistoriqueRecente(-1);
        $nombreHistorique = $this->modificationModel->countModificationHistorique();

        // Si des modifications historiques existent, elles sont enrichies avec des entités complètes
        if (!empty($modificationHistorique)) {
            $this->processModifications($modificationHistorique);
        }

        $data['modificationHistorique'] = $modificationHistorique;
        $data['nombreHistorique'] = $nombreHistorique;
        $data['activePage'] = 'moderation';
        return view('backoffice/historique', $data);
    }
}