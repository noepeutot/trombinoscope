<?php

namespace App\Controllers;

use App\Models\PersonneModel;
use App\Models\UtilisateurModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Session\Session;
use Config\Services;
use Psr\Log\LoggerInterface;

class Login extends BaseController
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
        return $this->login();
    }

    public function login() {
        $data = [];
        if ($this->session->get('user')) {
            return redirect('/');
        }

        if($this->request->getPost('login') && $this->request->getPost('password')) {
            $login = $this->request->getPost('login');
            $password = $this->request->getPost('password');
            //TODO : gérer le remember de login
            $remember = $this->request->getPost('remember') === 'on';
            $validUser = $this->personneModel->authValidateUser($login, $password);
            if ($validUser) {
                $user = $this->personneModel->getPersonneLogin($login);
                if(isset($user)) {
                    $role = $user->role;
                } else {
                    $role = 'normal';
                }
                $sessionData = [
                    'login' => $login,
                    'role' => $role,
                    'remember' => $remember
                ];
                var_dump($sessionData);
                $this->session->remove('error');
                $this->session->set('user', $sessionData);
                return redirect('/');
            } else {
                $this->session->set('error', 'Login ou mot de passe incorrect.');
                $data['error'] = $this->session->get('error');
            }
        }
        return view('login', $data);
    }

    /**
     * Fonction qui permet de se déconnecter en supprimer la session de l’utilisateur
     * @return RedirectResponse
     */
    public function logout()
    {
        $this->session->remove('user');
        return redirect('/');
    }
}