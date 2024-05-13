<?php

namespace App\Models;

use App\Entities\EncadrantEntity;
use CodeIgniter\Database\BaseResult;
use CodeIgniter\Model;
use ReflectionException;

class EncadrantModel extends Model
{
    protected $table = 'encadrant';

    protected $primaryKey = 'id_encadrant';

    protected $returnType = EncadrantEntity::class;

    protected $allowedFields = [
        'id_encadrant',
        'id_sejour',
        'id_personne',
        'nom',
        'prenom'
    ];

    public function __construct()
    {
        Parent::__construct();
    }

    /**
     * Fonction qui supprime tous les encadrants
     * @return void
     */
    public function deleteAll()
    {
        $encadrants = $this->getAllEncadrants();
        foreach ($encadrants as $encadrant) {
            $this->deleteEncadrant($encadrant->id_encadrant);
        }
    }

    /**
     * Fonction qui permet de retourner tous les encadrants
     * @return array
     */
    public function getAllEncadrants(): array
    {
        return $this->findAll();
    }

    /**
     * Fonction qui supprimer un encadrant
     * @param $id_encadrant
     * @return bool|BaseResult
     */
    public function deleteEncadrant($id_encadrant)
    {
        return $this->where('id_encadrant', $id_encadrant)->delete();
    }

    /**
     * Fonction qui met à jour un encadrant
     */
    public function updateEncadrant(int $id_encadrant, array $data): bool
    {
        try {
            return $this->update($id_encadrant, $data);
        } catch (ReflectionException $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Fonction qui permet de retourner un encadrant à partir de son id
     * @param int $id_encadrant
     * @return array
     */
    public function getEncadrant(int $id_encadrant): array
    {
        return $this->where('id_encadrant', $id_encadrant)->find();
    }

    /**
     * Fonction qui ajoute un nouvel encadrant
     */
    public function insertEncadrant(array $data): bool
    {
        try {
            return $this->insert($data);
        } catch (ReflectionException $exception) {
            return $exception->getMessage();
        }
    }
}