<?php

namespace App\Models;

use App\Entities\EmployeurEntity;
use CodeIgniter\Database\BaseResult;
use CodeIgniter\Model;
use ReflectionException;

class EmployeurModel extends Model
{
    protected $table = 'employeur';

    protected $primaryKey = 'id_employeur';

    protected $returnType = EmployeurEntity::class;

    protected $allowedFields = [
        'id_employeur',
        'nom',
        'nom_court'
    ];

    public function __construct()
    {
        Parent::__construct();
    }

    /**
     * Fonction qui supprime tous les employeurs
     * @return void
     */
    public function deleteAll()
    {
        $employeurs = $this->getAllEmployeurs();
        foreach ($employeurs as $employeur) {
            $this->deleteEmployeur($employeur->id_employeur);
        }
    }

    /**
     * Fonction qui permet de retourner tous les employeurs
     * @return array
     */
    public function getAllEmployeurs(): array
    {
        return $this->findAll();
    }

    /**
     * Fonction qui supprimer un employeur
     * @param $id_employeur
     * @return bool|BaseResult
     */
    public function deleteEmployeur($id_employeur)
    {
        return $this->where('id_employeur', $id_employeur)->delete();
    }

    /**
     * Fonction qui met à jour un employeur
     */
    public function updateEmployeur(int $id_employeur, array $data): bool
    {
        try {
            return $this->update($id_employeur, $data);
        } catch (ReflectionException $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Fonction qui permet de retourner un employeur à partir de son id
     * @param int $id_employeur
     * @return array|object|null
     */
    public function getEmployeur(int $id_employeur)
    {
        return $this->where('id_employeur', $id_employeur)->first();
    }

    /**
     * Fonction qui permet de retourner les employeurs de la personne
     * @param int $id_personne
     * @return array|object|null
     */
    public function getEmployeurPersonne(int $id_personne)
    {
        return $this->where("id_employeur IN
                (SELECT f.id_employeur
                FROM sejour s, financement f, employeur e
                WHERE s.id_personne=". $id_personne ."
                AND s.id_sejour=f.id_sejour
                AND f.id_employeur=e.id_employeur)")->find();
    }

    /**
     * Fonction qui ajoute un nouvel employeur
     */
    public function insertEmployeur(array $data): bool
    {
        try {
            return $this->insert($data);
        } catch (ReflectionException $exception) {
            return $exception->getMessage();
        }
    }
}