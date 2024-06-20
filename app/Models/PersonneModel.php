<?php

namespace App\Models;

use App\Entities\PersonneEntity;
use CodeIgniter\Database\BaseResult;
use CodeIgniter\Model;
use ReflectionException;

class PersonneModel extends Model
{
    protected $table = 'personne';

    protected $primaryKey = 'id_personne';

    protected $returnType = PersonneEntity::class;

    protected $allowedFields = [
        'id_personne',
        'login',
        'role',
        'nom',
        'prenom',
        'telephone',
        'statut',
        'bureau'];

    public function __construct()
    {
        Parent::__construct();
    }

    /**
     * Fonction qui permet de retourner une personne à partir de son id
     * @param int $id_personne
     * @return array|object|null
     */
    public function getPersonne(int $id_personne)
    {
        return $this->where('id_personne', $id_personne)->first();
    }

    public function getPersonneLogin(string $login)
    {
        return $this->where('login', $login)
            ->first();
    }

    /**
     * @param int $nombreLimite
     * @return array|null|object
     */
    public function getPersonneRecente(int $nombreLimite)
    {
        return $this->join('sejour', 'personne.id_personne = sejour.id_personne', 'inner')
            ->orderBy('date_debut', 'desc')
            ->limit($nombreLimite)
//            ->join('statut', 'personne.statut = statut.id_statut')
            ->join('mail', 'personne.id_personne = mail.id_personne', 'inner')
            ->find();
    }

    /**
     * Fonction qui ajoute ou met à jour les informations d’une personne
     * @param array $personne
     * @return bool
     * @throws ReflectionException
     */
    public function savePersonne(array $personne): bool
    {
        return $this->save($personne);
    }

    /**
     * Fonction qui met à jour les informations d’une personne
     */
    public function updatePersonne(int $id_personne, array $data): bool
    {
        try {
            return $this->update($id_personne, $data);
        } catch (ReflectionException $exception) {
            return false;
        }
    }

    /**
     * Fonction qui met à jour les informations de toutes les personnes
     */
    public function updateAll($update): bool
    {
        try {

            $allPersonnes = $this->getAllPersonnes('nom');
            foreach ($allPersonnes as $personne) {
                $this->update($personne->id_personne, $update);
            }
            return true;
        } catch (ReflectionException $exception) {
            return false;
        }
    }

    /**
     * Fonction qui permet de retourner toutes les personnes en triant par $orderBy et dans l’ordre $order
     * @param string $orderBy
     * @param string $order
     * @return array
     */
    public function getAllPersonnes(string $orderBy, string $order = 'ASC'): array
    {
        return $this->orderBy($orderBy, $order)
            ->orderBy('nom', 'ASC')
            ->findAll();
    }

    /**
     * Retourne une pagination de toutes les personnes avec leur séjour, mail et statut
     * @param string $orderBy est l’élément sur lequel il est ordonné
     * @param string $order est le sens de l’ordre
     * @param int $parPage est le nombre d’éléments affiché sur une seule même page
     * @return array
     */
    public function getAllPersonnesPagination(string $orderBy = 'role', string $order = 'ASC', int $parPage = 10): array
    {
        return $this->join('sejour', 'personne.id_personne = sejour.id_personne', 'inner')
            ->orderBy($orderBy, $order)
            ->join('mail', 'personne.id_personne = mail.id_personne', 'inner')
            ->join('statut', 'personne.statut=statut.id_statut', 'inner')
            ->paginate($parPage);
    }

    public function getPersonneByRolePagination(string $role = "normal", string $orderBy = 'role', string $order = 'ASC', int $parPage = 10): ?array
    {
        return $this->where('role', $role)
            ->join('sejour', 'personne.id_personne = sejour.id_personne', 'inner')
            ->orderBy($orderBy, $order)
            ->join('mail', 'personne.id_personne = mail.id_personne', 'inner')
            ->join('statut', 'personne.statut=statut.id_statut', 'inner')
            ->paginate($parPage);
    }

    /**
     * Retourne la recherche de personne à partir de la $query
     * @param string $query
     * @param string $role
     * @param int $parPage
     * @return array|null
     */
    public function searchPagination(string $query = "", string $role = "all", int $parPage = 10): ?array
    {
        $this->join('sejour', 'personne.id_personne = sejour.id_personne', 'inner')
            ->join('mail', 'personne.id_personne = mail.id_personne', 'inner')
            ->join('statut', 'personne.statut=statut.id_statut', 'inner')
            ->orderBy('nom', 'ASC');

        if (!empty($query)) {
            $queryString = explode(" ", $query);
            $this->groupStart();
            foreach ($queryString as $char) {
                $this->like('nom', $char)
                    ->orlike('prenom', $char);
            }
            $this->groupEnd();
        }

        switch ($role):
            case 'all':
                break;
            case 'admin':
                $this->where('role', 'admin');
                break;
            case 'modo':
                $this->where('role', 'modo');
                break;
            case 'normal':
                $this->where('role', 'normal');
                break;
        endswitch;

        return $this->paginate($parPage);
    }

    /**
     * Fonction qui ajoute une nouvelle personne
     */
    public function insertPersonne(array $data): bool
    {
        try {
            return $this->insert($data);
        } catch (ReflectionException $exception) {
            return false;
        }
    }

    /**
     * Fonction qui supprime toutes les personnes
     * @return void
     */
    public function deleteAll()
    {
        $personnes = $this->getAllPersonnes('nom');
        foreach ($personnes as $personne) {
            $this->deletePersonne($personne->id_personne);
        }
    }

    /**
     * Fonction qui supprimer une personne
     * @param $id_personne
     * @return bool|BaseResult
     */
    public function deletePersonne($id_personne)
    {
        return $this->where('id_personne', $id_personne)->delete();
    }

    /**
     * Fonction qui recherche les personnes en fonction de la query
     * et des filtres de status, d’équipes et de tuteurs
     * @param $query
     * @param $statuts
     * @param $equipes
     * @param $tuteurs
     * @return array|object|null
     */
    public function searchPersonne($query, $statuts, $equipes, $tuteurs)
    {
        // Filtre des noms et des prénoms
        if (!empty($query)) {
            $queryString = explode(" ", $query);
            foreach ($queryString as $char) {
                $this->like('nom', $char)
                    ->orLike('prenom', $char);
            }
        }

        // Filtre des status
        if (!empty($statuts)) {
            foreach ($statuts as $statut) {
                $this->orlike('statut', $statut);
            }
        }

        // Filtre des équipes
        if (!empty($equipes)) {
            foreach ($equipes as $equipe) {
                $this->orWhere('id_personne IN
                (SELECT s.id_personne
                FROM sejour s, personne p, rattachement r
                WHERE p.id_personne=s.id_personne
                AND s.id_sejour=r.id_sejour
                AND r.id_equipe=' . $equipe . ')');
            }
        }

        // Filtre des tuteurs
        if (!empty($tuteurs)) {
            foreach ($tuteurs as $tuteur) {
                $fullName = explode(" ", $tuteur);
                $prenom = $fullName[0];
                $nom = $fullName[1];
                $this->orWhere("id_personne IN
                (SELECT s.id_personne
                FROM sejour s, encadrant e, personne p
                WHERE s.id_sejour=e.id_sejour
                AND e.id_personne=p.id_personne
                AND p.prenom='" . $prenom . "'
                AND p.nom='" . $nom . "')");
            }
        }

        return $this->orderBy('nom')->find();
    }

    /**
     * Fonction qui retourne tous les encadrants
     * @return array
     */
    public function getAllEncadrants(): array
    {
        return $this->where('id_personne IN (SELECT id_personne FROM encadrant)')
            ->orderBy('nom', 'ASC')
            ->findAll();
    }

    /**
     * Fonction qui retourne les responsables d’une personne sur son séjour
     * @param int $id_personne
     * @param int $id_sejour
     * @return array|object|null
     */
    public function getResponsablePersonne(int $id_personne, int $id_sejour)
    {
        return $this->where("id_personne IN
                (SELECT e.id_personne
                FROM encadrant e, sejour s
                WHERE s.id_personne=" . $id_personne . "
                AND e.id_sejour=s.id_sejour
                AND e.id_sejour=" . $id_sejour . ")")
            ->find();
    }

    /**
     * Fonction qui retourne les personnes sous responsabilités de la personne mise en paramètre
     * @param int $id_personne
     * @return array|object|null
     */
    public function getEncadrePersonne(int $id_personne)
    {
        return $this->where("id_personne IN
            (SELECT p.id_personne 
            FROM encadrant e, sejour s, personne p
            WHERE e.id_personne=" . $id_personne . "
            AND e.id_sejour=s.id_sejour
            AND s.id_personne=p.id_personne)")
            ->find();
    }

    /**
     * Fonction qui vérifie le couple login/mdp.
     * Retourne true si l’authentification est bonne.
     * @param $username
     * @param $passwd
     * @return bool
     */
    function authValidateUser($username, $passwd): bool
    {
        $ldap_host[] = 'dc2016-1.g2elab.grenoble-inp.fr';
        $ldap_host[] = 'dc2019-1.g2elab.grenoble-inp.fr';
        $ldap_base_dn[] = 'OU=Utilisateurs,DC=g2elab,DC=local';
        $ldap_base_dn[] = 'CN=Users,DC=g2elab,DC=local';

        // Allow non-ascii in username & password.
        $username = utf8_decode($username);
        $passwd = utf8_decode($passwd);

        if (empty($username) || empty($passwd))
            return false;

        // On essaie de se connecter au premier serveur disponible.
        $bon = false;
        foreach ($ldap_host as $host) {
            if ($ldap = ldap_connect($host)) {
                $bon = true;
                break;
            }
        }
        if (!$bon) return false;

        // On teste le login/mot de passe
        foreach ($ldap_base_dn as $base_dn) {
            if (@ldap_bind($ldap, 'CN=' . $username . ',' . $base_dn, $passwd)) {
                @ldap_unbind($ldap);
                return True;
            }
        }
        // DN not found or password wrong.
        return False;
    }
}