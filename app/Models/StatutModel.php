<?php

namespace App\Models;

use App\Entities\StatutEntity;
use CodeIgniter\Database\BaseResult;
use CodeIgniter\Model;
use ReflectionException;

class StatutModel extends Model
{
    protected $table = 'statut';

    protected $primaryKey = 'id_statut';

    protected $returnType = StatutEntity::class;

    protected $allowedFields = [
        'id_statut',
        'nom'
    ];

    public function __construct()
    {
        Parent::__construct();
    }

    /**
     * Fonction qui supprime tous les statuts
     * @return void
     */
    public function deleteAll()
    {
        $statuts = $this->getAllStatuts();
        foreach ($statuts as $statut) {
            $this->deleteStatut($statut->id_statut);
        }
    }

    /**
     * Fonction qui permet de retourner tous les statuts
     * @return array
     */
    public function getAllStatuts(): array
    {
        return $this->orderBy('nom', 'ASC')->findAll();
    }

    /**
     * Fonction qui supprimer un statut
     * @param $id_statut
     * @return bool|BaseResult
     */
    public function deleteStatut($id_statut)
    {
        return $this->where('id_statut', $id_statut)->delete();
    }

    /**
     * Fonction qui met à jour un statut
     */
    public function updateStatut(int $id_statut, array $data): bool
    {
        try {
            return $this->update($id_statut, $data);
        } catch (ReflectionException $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Fonction qui permet de retourner un statut à partir de son id
     * @param int $id_statut
     * @return object|null
     */
    public function getStatut(int $id_statut): ?object
    {
        return $this->where('id_statut', $id_statut)->first();
    }

    /**
     * Fonction qui retourne le statut d’une personne à partir de son id
     * @param int $id_personne
     * @return array|object|null
     */
    public function getStatutPersonne(int $id_personne)
    {
        return $this->where('id_statut IN 
        (SELECT s.id_statut 
        FROM statut s, personne p 
        WHERE s.id_statut=p.statut
        AND p.id_personne='. $id_personne.')')->first();
    }

    /**
     * Fonction qui ajoute un nouveau statut
     */
    public function insertStatut(array $data): bool
    {
        try {
            return $this->insert($data);
        } catch (ReflectionException $exception) {
            return $exception->getMessage();
        }
    }
}