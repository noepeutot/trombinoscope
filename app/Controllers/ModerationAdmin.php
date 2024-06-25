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

    public function index()
    {
        $user = $this->session->get('user');
        if ($user) {
            $personneConnectee = $this->personneModel->getPersonneLogin($user['login']);
            if ($personneConnectee && ($personneConnectee->role === 'admin' || $personneConnectee->role === 'modo')) {
                switch (uri_string()) {
                    case 'backoffice/moderation':
                        return $this->moderation();

                    case 'backoffice/moderation/en-attente':
                        return $this->attente();
                }
            }
        }
        return redirect('/');
    }

    public function moderation(): string
    {
        $data = [];

        $modificationEnAttente = $this->modificationModel->getModificationEnAttenteRecente(4);
        $ModificationHistorique = $this->modificationModel->getModificationHistoriqueRecente(4);
        $nombreEnAttente = $this->modificationModel->countModificationEnAttente();
        $nombreHistorique = $this->modificationModel->countModificationHistorique();
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

    public function attente(): string
    {
        $data = [];

        if($this->request->getPost()) {
            if($this->request->getPost('annule')){
                $id_modification = $this->request->getPost('annule');
                $modification = $this->modificationModel->getModification($id_modification);
                if($modification) {
                    $this->modificationModel->updateModification($id_modification, ['statut' => 'annule']);
                }
            } elseif ($this->request->getPost('valide')) {
                $id_modification = $this->request->getPost('valide');
                $modification = $this->modificationModel->getModification($id_modification);
                if($modification) {
                    $attribut = $modification->attribut;
                    switch ($attribut){
                        case 'Nom':
                            $update = ['nom' => $modification->apres];
                            $this->personneModel->updatePersonne($modification->id_personne, $update);
                            break;
                        case 'Prénom':
                            $update = ['prenom' => $modification->apres];
                            $this->personneModel->updatePersonne($modification->id_personne, $update);
                            break;
                        case 'Téléphone':
                            $update = ['telephone' => $modification->apres];
                            $this->personneModel->updatePersonne($modification->id_personne, $update);
                            break;
                        case 'Bureau':
                            $update = ['bureau' => intval($modification->apres)];
                            $this->personneModel->updatePersonne($modification->id_personne, $update);
                            break;
                        case 'Statut':
                            $update = ['statut' => intval($modification->apres)];
                            $this->personneModel->updatePersonne($modification->id_personne, $update);
                            break;
                        case 'Mail':
                            $update = ['libelle' => $modification->apres];
                            $this->mailModel->updateMailPersonne($modification->id_personne, $update);
                            break;
                        case 'Activité':
                            $update = ['sujet' => $modification->apres];
                            $sejour = $this->sejourModel->getSejourPersonne($modification->id_personne);
                            if($sejour){
                                $this->sejourModel->updateSejour($sejour->id_sejour, $update);
                            }
                            break;
                        case 'Employeur':
                            // TODO : A finir
//                            var_dump($modification->apres);
//                            $employeursID = explode(', ', $modification->apres);
//                            foreach ($employeursID as $employeurID) {
//                                $update = ['id_employeur' => intval($employeurID)];
//                                $sejour = $this->sejourModel->getSejourPersonne($modification->id_personne);
//                                if ($sejour) {
//                                    $this->financementModel->updateFinancementSejour($sejour->id_sejour, $update);
//                                }
//                            }
                            break;
                        case 'Equipe':
//                            $update = ['sujet' => $modification->apres];
//                            $sejour = $this->sejourModel->getSejourPersonne($modification->id_personne);
//                            if($sejour) {
//                                $this->financementModel->updateFinancementSejour($sejour->id_sejour, $update);
//                            }
                            break;
                        case 'Photo':
                            break;
                    }
                    $this->modificationModel->updateModification($id_modification, ['statut' => 'valide']);
                }
            }
        }

        $modificationEnAttente = $this->modificationModel->getModificationEnAttenteRecente(-1);
        $nombreEnAttente = $this->modificationModel->countModificationEnAttente();
        if (!empty($modificationEnAttente)) {
            $this->processModifications($modificationEnAttente);
        }

        $data['modificationEnAttente'] = $modificationEnAttente;
        $data['nombreEnAttente'] = $nombreEnAttente;
        $data['activePage'] = 'moderation';
        return view('backoffice/en-attente', $data);
    }

    public function enrichModification($modification) {
        $personne = $this->personneModel->getPersonne($modification->id_personne);
        $modification->nom = $personne->nom;
        $modification->prenom = $personne->prenom;

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

    private function enrichBureau($modification) {
        $bureauAvant = $this->bureauModel->getBureau(intval($modification->avant));
        $bureauApres = $this->bureauModel->getBureau(intval($modification->apres));
        $modification->bureauAvant = $bureauAvant;
        $modification->bureauApres = $bureauApres;
    }

    private function enrichStatut($modification) {
        $statutAvant = $this->statutModel->getStatut(intval($modification->avant));
        $statutApres = $this->statutModel->getStatut(intval($modification->apres));
        $modification->statutAvant = $statutAvant;
        $modification->statutApres = $statutApres;
    }

    private function enrichEquipe($modification) {
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

        $modification->equipeAvant = $equipesAvant;
        $modification->equipeApres = $equipesApres;
    }

    private function enrichEmployeur($modification) {
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

        $modification->employeurAvant = $employeurAvant;
        $modification->employeurApres = $employeurApres;
    }

    public function processModifications($modifications) {
        foreach ($modifications as $modification) {
            $this->enrichModification($modification);
        }
    }
}