<?php

namespace App\Controllers;

use App\Models\PersonneModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Session\Session;
use Config\Services;
use Psr\Log\LoggerInterface;

class ModerationAdmin extends BaseController
{
    protected Session $session;
    protected PersonneModel $personneModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->session = Services::session();
        $this->personneModel = new PersonneModel();
    }

    public function index()
    {
        $user = $this->session->get('user');
        if ($user) {
            $personneConnectee = $this->personneModel->getPersonneLogin($user['login']);
            if ($personneConnectee && ($personneConnectee->role === 'admin' || $personneConnectee->role === 'modo')) {
                return $this->moderation();
            }
        }
        return redirect('/');
    }

    public function moderation(): string
    {
        $data = [];
        $data['activePage'] = 'moderation';
        return view('backoffice/dashboard', $data);
    }
}