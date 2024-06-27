<?php

namespace App\Controllers;

use App\Models\PersonneModel;
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

    /**
     * Méthode pour gérer le processus de connexion
     * @return RedirectResponse|string
     */
    public function login() {
        $data = [];

        // Vérification si un utilisateur est déjà connecté
        if ($this->session->get('user')) {
            return redirect('/');
        }

        // Vérification si les informations de connexion ont été postées
        if($this->request->getPost('login') && $this->request->getPost('password')) {
            // Récupération des données de connexion postées
            $login = $this->request->getPost('login');
            $password = $this->request->getPost('password');

            // Validation des informations de connexion
            $validUser = $this->personneModel->authValidateUser($login, $password);
            if ($validUser) {
                // Récupération des informations de l’utilisateur à partir de son login
                $user = $this->personneModel->getPersonneLogin($login);
                if(isset($user)) {
                    $role = $user->role;
                } else {
                    $role = 'normal';
                }

                // Préparation des données de session
                $sessionData = [
                    'login' => $login,
                    'role' => $role
                ];

                // Suppression de l’erreur de session s’il y en a une
                $this->session->remove('error');
                // Définition des données de session pour l’utilisateur connecté
                $this->session->set('user', $sessionData);

                // Redirection vers la page d’accueil après connexion réussie
                return redirect('/');
            } else {
                // Définition d’un message d’erreur en session si la connexion échoue
                $this->session->set('error', 'Login ou mot de passe incorrect.');
                $data['error'] = $this->session->get('error');
            }
        }
        return view('frontoffice/login', $data);
    }

    /**
     * Fonction qui permet de se déconnecter
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        // Destruction de la session pour déconnecter l’utilisateur
        $this->session->destroy();

        // Redirection vers la page d’accueil après déconnexion
        return redirect('/');
    }
}