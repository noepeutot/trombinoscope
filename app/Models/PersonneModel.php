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
        'nom',
        'prenom',
        'telephone',
        'statut',
        'equipe',
        'numero_bureau'];

    public function __construct()
    {
        Parent::__construct();
    }

    /**
     * Fonction qui permet de retourner une personne à partir de son id
     * @param int $id_personne
     * @return array
     */
    public function getPersonne(int $id_personne): array
    {
        return $this->where('id_personne', $id_personne)->find();
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
            $this->deletePersonne($personne['id_personne']);
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
}