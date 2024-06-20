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

    public function index()
    {
        $user = $this->session->get('user');
        if ($user) {
            $personneConnectee = $this->personneModel->getPersonneLogin($user['login']);
            if ($personneConnectee && ($personneConnectee->role === 'admin')) {
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
        return redirect('/');
    }

    public function users(): string
    {
        $data = [];

        if ($this->request->getGetPost('q') && !empty($this->request->getGetPost('q'))) {
            return $this->search('all');
        }

        $users = $this->personneModel->getAllPersonnesPagination('role', 'DESC', 11);
        $pager = $this->personneModel->pager;

        $data['activePage'] = 'users';
        $data['users'] = $users;
        $data['pager'] = $pager;
        return view('backoffice/users', $data);
    }

    public function search(string $type): string
    {
        $data = [];

        $query = $this->request->getGetPost('q');
        $users = $this->personneModel->searchPagination($query, $type, 11);
        $pager = $this->personneModel->pager;

        $data['activePage'] = 'users';
        $data['query'] = $query;
        $data['users'] = $users;
        $data['pager'] = $pager;
        return view('backoffice/users', $data);
    }

    public function admin(): string
    {
        $data = [];

        if ($this->request->getGetPost('q') && !empty($this->request->getGetPost('q'))) {
            return $this->search('admin');
        }

        $users = $this->personneModel->getPersonneByRolePagination('admin', 'role', 'DESC', 11);
        $pager = $this->personneModel->pager;

        $data['activePage'] = 'users';
        $data['users'] = $users;
        $data['pager'] = $pager;
        return view('backoffice/users', $data);
    }

    public function modo(): string
    {
        $data = [];

        if ($this->request->getGetPost('q') && !empty($this->request->getGetPost('q'))) {
            return $this->search('modo');
        }

        $users = $this->personneModel->getPersonneByRolePagination('modo', 'role', 'DESC', 11);
        $pager = $this->personneModel->pager;

        $data['activePage'] = 'users';
        $data['users'] = $users;
        $data['pager'] = $pager;
        return view('backoffice/users', $data);
    }

    public function normal(): string
    {
        $data = [];

        if ($this->request->getGetPost('q') && !empty($this->request->getGetPost('q'))) {
            return $this->search('normal');
        }

        $users = $this->personneModel->getPersonneByRolePagination('normal', 'role', 'DESC', 11);
        $pager = $this->personneModel->pager;

        $data['activePage'] = 'users';
        $data['users'] = $users;
        $data['pager'] = $pager;
        return view('backoffice/users', $data);
    }

    public function changerRole(): RedirectResponse
    {
        if (!$this->request->getGetPost('role')) {
            return redirect('backoffice/users');
        }

        $query = $this->request->getGetPost('role');
        $queryString = explode(" ", $query);
        $role = $queryString[0];
        $idPersonne = $queryString[1];
        $update = ['role' => $role];
        $this->personneModel->updatePersonne($idPersonne, $update);

        return redirect('backoffice/users');
    }
}