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

            $allPersonnes = $this->getAllPersonnes();
            foreach ($allPersonnes as $personne) {
                $this->update($personne->id_personne, $update);
            }
            return true;
        } catch (ReflectionException $exception) {
            return false;
        }
    }

    /**
     * Fonction qui permet de retourner toutes les personnes
     * @return array
     */
    public function getAllPersonnes(): array
    {
        return $this->orderBy('nom')->findAll();
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
        $personnes = $this->getAllPersonnes();
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
     * @return array|float[]|float[][]|int[]|int[][]|null[]|null[][]|object|object[]|object[][]|string[]|string[][]|null
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
                $this->orlike('equipe', $equipe);
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
        return $this->where('id_personne IN (SELECT id_personne FROM encadrant)')->find();
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
        $username=utf8_decode($username);
        $passwd=utf8_decode($passwd);

        if(empty($username)||empty($passwd))
            return false;

        // On essaie de se connecter au premier serveur disponible.
        $bon=false;
        foreach($ldap_host as $host)
        {
            if($ldap = ldap_connect ($host))
            {
                $bon=true;
                break;
            }
        }
        if(!$bon) return false;

        // On teste le login/mot de passe
        foreach( $ldap_base_dn as $base_dn)
        {
            if (@ldap_bind($ldap, 'CN='.$username.','.$base_dn, $passwd))
            {
                @ldap_unbind($ldap);
                return True;
            }
        }
        // DN not found or password wrong.
        return False;
    }
}