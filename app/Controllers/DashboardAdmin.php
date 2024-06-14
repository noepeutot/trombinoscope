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
        $user = $this->session->get('user');
        if ($user) {
            $personneConnectee = $this->personneModel->getPersonneLogin($user['login']);
            if ($personneConnectee && ($personneConnectee->role === 'admin' || $personneConnectee->role === 'modo')) {
                return $this->dashboard();
            }
        }
        return redirect('/');
    }

    public function dashboard(): string
    {
        $data = [];

        $personneRecente = $this->personneModel->getPersonneRecente(5);
        $modificationRecente = $this->modificationModel->getModificationRecente(4);

        if (!empty($modificationRecente)) {
            foreach ($modificationRecente as $modification) {
                // Attribution de la personne dans l’array pour récupérer le nom et prénom
                $personne = $this->personneModel->getPersonne($modification->id_personne);
                $modification->nom = $personne->nom;
                $modification->prenom = $personne->prenom;

                if ($modification->attribut === "Bureau") {
                    $numeroBureauAvant = intval($modification->avant);
                    $bureauAvant = $this->bureauModel->getBureau($numeroBureauAvant);

                    $numeroBureauApres = intval($modification->apres);
                    $bureauApres = $this->bureauModel->getBureau($numeroBureauApres);

                    $data['bureauAvant'] = $bureauAvant;
                    $data['bureauApres'] = $bureauApres;
                } elseif ($modification->attribut === "Statut") {
                    $IDstatutAvant = intval($modification->avant);
                    $statutAvant = $this->statutModel->getStatut($IDstatutAvant);

                    $IDstatutApres = intval($modification->apres);
                    $statutApres = $this->statutModel->getStatut($IDstatutApres);

                    $data['statutAvant'] = $statutAvant;
                    $data['statutApres'] = $statutApres;
                } elseif ($modification->attribut === "Photo") {

                } elseif ($modification->attribut === "Equipe") {
                    $equipesAvant = [];
                    $equipesID = explode(', ', $modification->avant);
                    foreach ($equipesID as $equipeID) {
                        $equipesAvant[] = $this->equipeModel->getEquipe(intval($equipeID));
                    }

                    $equipesApres = [];
                    $equipesID = explode(', ', $modification->apres);
                    foreach ($equipesID as $equipeID) {
                        $equipesApres[] = $this->equipeModel->getEquipe(intval($equipeID));
                    }

                    $data['equipeAvant'] = $equipesAvant;
                    $data['equipeApres'] = $equipesApres;
                } elseif ($modification->attribut === "Employeur") {
                    $employeurAvant = [];
                    $employeursID = explode(', ', $modification->avant);
                    foreach ($employeursID as $employeurID) {
                        $employeurAvant[] = $this->employeurModel->getEmployeur(intval($employeurID));
                    }

                    $employeurApres = [];
                    $employeursID = explode(', ', $modification->apres);
                    foreach ($employeursID as $employeurID) {
                        $employeurApres[] = $this->employeurModel->getEmployeur(intval($employeurID));
                    }

                    $data['employeurAvant'] = $employeurAvant;
                    $data['employeurApres'] = $employeurApres;
                }
            }
        }

        $data['personneRecente'] = $personneRecente;
        $data['modificationRecente'] = $modificationRecente;
        $data['activePage'] = 'dashboard';
        return view('backoffice/dashboard', $data);
    }
}