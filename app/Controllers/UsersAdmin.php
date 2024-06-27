<?php

namespace App\Controllers;

use App\Models\PersonneModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Session\Session;
use Config\Services;
use Psr\Log\LoggerInterface;

class UsersAdmin extends BaseController
{
    protected Session $session;
    protected PersonneModel $personneModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->session = Services::session();
        $this->personneModel = new PersonneModel();
    }

    /**
     * Méthode pour afficher la page principale de gestion des utilisateurs
     * @return RedirectResponse|string
     */
    public function index()
    {
        // Récupération de l’utilisateur connecté depuis la session
        $user = $this->session->get('user');

        // Vérification si un utilisateur est connecté et s’il a le rôle d’administrateur
        if ($user) {
            $personneConnectee = $this->personneModel->getPersonneLogin($user['login']);
            if ($personneConnectee && ($personneConnectee->role === 'admin')) {
                // Redirection en fonction de l’URI demandé
                switch (uri_string()) {
                    case 'backoffice/users':
                        return $this->users();

                    case 'backoffice/users/admin':
                        return $this->admin();

                    case 'backoffice/users/modo':
                        return $this->modo();

                    case 'backoffice/users/normal':
                        return $this->normal();

                    case 'backoffice/users/changer-role':
                        return $this->changerRole();
                }
            }
        }

        // Redirection vers la page d’accueil si l’utilisateur n’est pas autorisé
        return redirect('/');
    }

    /**
     * Méthode pour afficher tous les utilisateurs
     * @return string
     */
    public function users(): string
    {
        $data = [];

        // Vérification si une recherche a été effectuée
        if ($this->request->getGetPost('q') && !empty($this->request->getGetPost('q'))) {
            // Faire une recherche sur l’entièreté des utilisateurs
            return $this->search('all');
        }

        // Récupération des utilisateurs avec pagination
        $users = $this->personneModel->getAllPersonnesPagination('role', 'DESC', 11);
        $pager = $this->personneModel->pager;

        // Préparation des données pour la vue
        $data['activePage'] = 'users';
        $data['users'] = $users;
        $data['pager'] = $pager;
        return view('backoffice/users', $data);
    }

    /**
     * Méthode pour rechercher des utilisateurs en fonction des types (des onglets : all, admin, modo, normal)
     * @param string $type
     * @return string
     */
    public function search(string $type): string
    {
        $data = [];

        // Récupération de la requête de recherche
        $query = $this->request->getGetPost('q');
        $users = $this->personneModel->searchPagination($query, $type, 11);
        $pager = $this->personneModel->pager;

        // Préparation des données pour la vue
        $data['activePage'] = 'users';
        $data['query'] = $query;
        $data['users'] = $users;
        $data['pager'] = $pager;
        return view('backoffice/users', $data);
    }

    /**
     * Méthode pour afficher les utilisateurs ayant le rôle d’administrateur
     * @return string
     */
    public function admin(): string
    {
        $data = [];

        // Vérification si une recherche a été effectuée
        if ($this->request->getGetPost('q') && !empty($this->request->getGetPost('q'))) {
            return $this->search('admin');
        }

        // Récupération des administrateurs avec pagination
        $users = $this->personneModel->getPersonneByRolePagination('admin', 'role', 'DESC', 11);
        $pager = $this->personneModel->pager;

        // Préparation des données pour la vue
        $data['activePage'] = 'users';
        $data['users'] = $users;
        $data['pager'] = $pager;
        return view('backoffice/users', $data);
    }

    /**
     * Méthode pour afficher les utilisateurs ayant le rôle de modérateur
     * @return string
     */
    public function modo(): string
    {
        $data = [];

        // Vérification si une recherche a été effectuée
        if ($this->request->getGetPost('q') && !empty($this->request->getGetPost('q'))) {
            return $this->search('modo');
        }

        // Récupération des modérateurs avec pagination
        $users = $this->personneModel->getPersonneByRolePagination('modo', 'role', 'DESC', 11);
        $pager = $this->personneModel->pager;

        // Préparation des données pour la vue
        $data['activePage'] = 'users';
        $data['users'] = $users;
        $data['pager'] = $pager;
        return view('backoffice/users', $data);
    }

    /**
     * Méthode pour afficher les utilisateurs ayant le rôle normal
     * @return string
     */
    public function normal(): string
    {
        $data = [];

        // Vérification si une recherche a été effectuée
        if ($this->request->getGetPost('q') && !empty($this->request->getGetPost('q'))) {
            return $this->search('normal');
        }

        // Récupération des utilisateurs normaux avec pagination
        $users = $this->personneModel->getPersonneByRolePagination('normal', 'role', 'DESC', 11);
        $pager = $this->personneModel->pager;

        // Préparation des données pour la vue
        $data['activePage'] = 'users';
        $data['users'] = $users;
        $data['pager'] = $pager;
        return view('backoffice/users', $data);
    }

    /**
     * Méthode pour changer le rôle d’un utilisateur
     * @return RedirectResponse
     */
    public function changerRole(): RedirectResponse
    {
        // Vérification si le paramètre 'role' est présent dans la requête
        if (!$this->request->getGetPost('role')) {
            return redirect('backoffice/users');
        }

        // Récupération des informations du rôle et de l’ID de l’utilisateur à partir de la requête
        $query = $this->request->getGetPost('role');
        $queryString = explode(" ", $query);
        $role = $queryString[0];
        $idPersonne = $queryString[1];

        // Mise à jour du rôle de l’utilisateur
        $update = ['role' => $role];
        $this->personneModel->updatePersonne($idPersonne, $update);

        // Redirection vers la page des utilisateurs après la mise à jour
        return redirect('backoffice/users');
    }
}