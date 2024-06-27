<?php

namespace App\Controllers;

use App\Models\BureauModel;
use App\Models\EmployeurModel;
use App\Models\EquipeModel;
use App\Models\FinancementModel;
use App\Models\MailModel;
use App\Models\ModificationModel;
use App\Models\PersonneModel;
use App\Models\StatutModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Session\Session;
use Config\Services;
use Psr\Log\LoggerInterface;

class DashboardAdmin extends BaseController
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
    }

    public function index()
    {
        // Récupération de l’utilisateur connecté depuis la session
        $user = $this->session->get('user');

        // Vérification si un utilisateur est connecté et s’il a les rôles requis (admin ou modo)
        if ($user) {
            $personneConnectee = $this->personneModel->getPersonneLogin($user['login']);
            if ($personneConnectee && ($personneConnectee->role === 'admin' || $personneConnectee->role === 'modo')) {
                return $this->dashboard();
            }
        }

        // Redirection vers la page d’accueil si l’utilisateur n’est pas connecté ou n’a pas les droits
        return redirect('/');
    }

    // Méthode pour afficher le tableau de bord avec les données nécessaires
    public function dashboard(): string
    {
        $data = [];

        // Récupération des 5 personnes les plus récemment ajoutées
        $personneRecente = $this->personneModel->getPersonneRecente(5);

        // Récupération des 4 dernières modifications
        $modificationRecente = $this->modificationModel->getModificationRecente(4);

        if (!empty($modificationRecente)) {
            foreach ($modificationRecente as $modification) {
                // Attribution de la personne à chaque modification pour obtenir le nom et le prénom
                $personne = $this->personneModel->getPersonne($modification->id_personne);
                $modification->nom = $personne->nom;
                $modification->prenom = $personne->prenom;

                // Traitement spécifique en fonction de l’attribut modifié
                if ($modification->attribut === "Bureau") {
                    // Récupération des bureaux avant et après la modification
                    // Convertir les ID avant et après la modification qui sont des strings en entiers
                    $numeroBureauAvant = intval($modification->avant);
                    $bureauAvant = $this->bureauModel->getBureau($numeroBureauAvant);

                    $numeroBureauApres = intval($modification->apres);
                    $bureauApres = $this->bureauModel->getBureau($numeroBureauApres);

                    // Stockage des entités bureau d’avant et d’après de la modification
                    $modification->bureauAvant = $bureauApres;
                    $modification->bureauApres = $bureauAvant;
                } elseif ($modification->attribut === "Statut") {
                    // Récupération des statuts avant et après la modification
                    // Convertir les ID avant et après la modification qui sont des strings en entiers
                    $IDstatutAvant = intval($modification->avant);
                    $statutAvant = $this->statutModel->getStatut($IDstatutAvant);

                    $IDstatutApres = intval($modification->apres);
                    $statutApres = $this->statutModel->getStatut($IDstatutApres);

                    // Stockage des entités statut d’avant et d’après de la modification
                    $modification->statutAvant = $statutAvant;
                    $modification->statutApres = $statutApres;
                } elseif ($modification->attribut === "Equipe") {
                    // Récupération des équipes avant la modification
                    // Séparer les ID avant la modification qui sont des strings en un tableau d’ID
                    $equipesAvant = [];
                    $equipesID = explode(', ', $modification->avant);
                    foreach ($equipesID as $equipeID) {
                        // Récupérer les entités de chaque équipe avant la modification
                        $equipesAvant[] = $this->equipeModel->getEquipe(intval($equipeID));
                    }

                    // Récupération des équipes après la modification
                    // Séparer les ID après la modification qui sont des strings en un tableau d’ID
                    $equipesApres = [];
                    $equipesID = explode(', ', $modification->apres);
                    foreach ($equipesID as $equipeID) {
                        $equipesApres[] = $this->equipeModel->getEquipe(intval($equipeID));
                    }

                    // Stockage des entités équipe d’avant et d’après de la modification
                    $modification->equipeAvant = $equipesAvant;
                    $modification->equipeApres = $equipesApres;
                } elseif ($modification->attribut === "Employeur") {
                    // Récupération des employeurs avant la modification
                    // Séparer les ID avant la modification qui sont des strings en un tableau d’ID
                    $employeurAvant = [];
                    $employeursID = explode(', ', $modification->avant);
                    foreach ($employeursID as $employeurID) {
                        // Récupérer les détails de chaque employeur avant la modification
                        $employeurAvant[] = $this->employeurModel->getEmployeur(intval($employeurID));
                    }

                    // Récupération des employeurs après la modification
                    // Séparer les ID après la modification qui sont des strings en un tableau d’ID
                    $employeurApres = [];
                    $employeursID = explode(', ', $modification->apres);
                    foreach ($employeursID as $employeurID) {
                        // Récupérer les détails de chaque employeur après la modification
                        $employeurApres[] = $this->employeurModel->getEmployeur(intval($employeurID));
                    }

                    // Stockage des entités employeur d’avant et d’après de la modification
                    $modification->employeurAvant = $employeurAvant;
                    $modification->employeurApres = $employeurApres;
                }
            }
        }

        $data['personneRecente'] = $personneRecente;
        $data['modificationRecente'] = $modificationRecente;
        $data['activePage'] = 'dashboard';
        return view('backoffice/dashboard', $data);
    }
}