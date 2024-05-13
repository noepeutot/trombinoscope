<?php

namespace App\Controllers;

use App\Models\APIModel;
use App\Models\EmployeurModel;
use App\Models\EncadrantModel;
use App\Models\FinancementModel;
use App\Models\MailModel;
use App\Models\PersonneModel;
use App\Models\ResponsabiliteModel;
use App\Models\SejourModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
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
    }

    public function index($id): string
    {
        $this->personneID = $id;
        $data = [];

        $personne = $this->personneModel->getPersonne($this->personneID);
        $mails = $this->mailModel->getMailPersonne($this->personneID);

        $employeurs = $this->employeurModel->getEmployeurPersonne($this->personneID);
        $sejour = $this->sejourModel->getSejourPersonne($this->personneID);

        $responsabilites = $this->responsabiliteModel->getResponsabilitePersonne($this->personneID);

        if (!empty($personne)) {
            $data['personne'] = $personne;
            $data['encadres'] = $this->personneModel->getEncadrePersonne($personne->id_personne);
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

        if (!empty($sejour) && !empty($personne)) {
            $data['responsables'] = $this->personneModel->getResponsablePersonne($personne->id_personne, $sejour->id_sejour);
        }

        return view('profile', $data);
    }

    public function beautifulPrint($data)
    {
        print("<pre>" . print_r($data, true) . "</pre>");
    }
}