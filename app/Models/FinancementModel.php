<?php

namespace App\Models;

use App\Entities\FinancementEntity;
use CodeIgniter\Database\BaseResult;
use CodeIgniter\Model;
use ReflectionException;

class FinancementModel extends Model
{
    protected $table = 'financement';

    protected $primaryKey = 'id_financement';

    protected $returnType = FinancementEntity::class;

    protected $allowedFields = [
        'id_financement',
        'id_sejour',
        'id_employeur'
    ];

    public function __construct()
    {
        Parent::__construct();
    }

    /**
     * Fonction qui supprime tous les financements
     * @return void
     */
    public function deleteAll()
    {
        $financements = $this->getAllFinancements();
        foreach ($financements as $financement) {
            $this->deleteFinancement($financement->id_financement);
        }
    }

    /**
     * Fonction qui permet de retourner tous les financements
     * @return array
     */
    public function getAllFinancements(): array
    {
        return $this->findAll();
    }

    /**
     * Fonction qui supprimer un financement
     * @param $id_financement
     * @return bool|BaseResult
     */
    public function deleteFinancement($id_financement)
    {
        return $this->where('id_financement', $id_financement)->delete();
    }

    /**
     * Fonction qui met à jour un financement
     */
    public function updateFinancement(int $id_financement, array $data): bool
    {
        try {
            return $this->update($id_financement, $data);
        } catch (ReflectionException $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Fonction qui permet de retourner un financement à partir de son id
     * @param int $id_financement
     * @return array
     */
    public function getFinancement(int $id_financement): array
    {
        return $this->where('id_financement', $id_financement)->find();
    }

    /**
     * Fonction qui ajoute un nouveau financement
     */
    public function insertFinancement(array $data): bool
    {
        try {
            return $this->insert($data);
        } catch (ReflectionException $exception) {
            return $exception->getMessage();
        }
    }
}