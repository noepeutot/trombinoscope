<?php

namespace App\Controllers;

use App\Models\APIModel;
use App\Models\BureauModel;
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

        $this->session = Services::session();
    }

    public function index($id): string
    {
        return $this->profile($id);
    }

    /** Fonction qui permet de gérer l’affichage du profile
     * @param $id
     * @return string
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
        $mail = $this->mailModel->getMailPersonne($this->personneID);

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
                $allEmployeurs = $this->employeurModel->getAllEmployeurs();

                $sejourPersonne = $this->sejourModel->getSejourPersonne($this->personneID);

                $responsabilitesPersonne = $this->responsabiliteModel->getResponsabilitePersonne($this->personneID);

                $statutPersonne = $this->statutModel->getStatutPersonne($this->personneID);
                $allStatuts = $this->statutModel->getAllStatuts();

                $equipePersonne = $this->equipeModel->getEquipePersonne($this->personneID);
                $allEquipes = $this->equipeModel->getAllEquipes();

                $encadresPersonne = $this->personneModel->getEncadrePersonne($this->personneID);

                $bureauPersonne = $this->bureauModel->getBureauPersonne($this->personneID);
                $allBureaux = $this->bureauModel->getAllBureaux();


                if (!empty($personne)) {
                    $data['personne'] = $personne;
                }

                if (!empty($statutPersonne)) {
                    $data['statutPersonne'] = $statutPersonne;
                }

                if (!empty($allStatuts)){
                    $data['allStatuts'] = $allStatuts;
                }

                if (!empty($mailPersonne)) {
                    $data['mailPersonne'] = $mailPersonne;
                }

                if (!empty($employeursPersonne)) {
                    $data['employeursPersonne'] = $employeursPersonne;
                }

                if (!empty($allEmployeurs)){
                    $data['allEmployeurs'] = $allEmployeurs;
                }

                if (!empty($responsabilitesPersonne)) {
                    $data['responsabilitesPersonne'] = $responsabilitesPersonne;
                }

                if (!empty($sejourPersonne)) {
                    $data['sejourPersonne'] = $sejourPersonne;
                    $data['responsablesPersonne'] = $this->personneModel->getResponsablePersonne($this->personneID, $sejourPersonne->id_sejour);
                }

                if(!empty($encadresPersonne)) {
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