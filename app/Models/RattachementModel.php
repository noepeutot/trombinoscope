<?php

namespace App\Models;

use CodeIgniter\Database\BaseResult;
use CodeIgniter\Model;
use ReflectionException;

class RattachementModel extends Model
{
    protected $table = 'rattachement';

    protected $primaryKey = 'id_rattachement';

    protected $returnType = RattachementModel::class;

    protected $allowedFields = [
        'id_rattachement',
        'id_sejour',
        'id_equipe'
    ];

    public function __construct()
    {
        Parent::__construct();
    }

    /**
     * Fonction qui supprime tous les rattachements
     * @return void
     */
    public function deleteAll()
    {
        $rattachements = $this->getAllRattachements();
        foreach ($rattachements as $rattachement) {
            $this->deleteRattachement($rattachement->id_rattachement);
        }
    }

    /**
     * Fonction qui permet de retourner tous les rattachements
     * @return array
     */
    public function getAllRattachements(): array
    {
        return $this->findAll();
    }

    /**
     * Fonction qui supprimer un rattachement
     * @param $id_rattachement
     * @return bool|BaseResult
     */
    public function deleteRattachement($id_rattachement)
    {
        return $this->where('id_rattachement', $id_rattachement)->delete();
    }

    /**
     * Fonction qui met à jour un rattachement
     */
    public function updateRattachement(int $id_rattachement, array $data): bool
    {
        try {
            return $this->update($id_rattachement, $data);
        } catch (ReflectionException $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Fonction qui permet de retourner un rattachement à partir de son id
     * @param int $id_rattachement
     * @return array|object|null
     */
    public function getRattachement(int $id_rattachement)
    {
        return $this->where('id_rattachement', $id_rattachement)->first();
    }

    /**
     * Fonction qui ajoute un nouveau rattachement
     */
    public function insertRattachement(array $data): bool
    {
        try {
            return $this->insert($data);
        } catch (ReflectionException $exception) {
            return $exception->getMessage();
        }
    }
}