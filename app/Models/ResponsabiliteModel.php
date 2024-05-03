<?php

namespace App\Models;

use App\Entities\ResponsabiliteEntity;
use CodeIgniter\Database\BaseResult;
use CodeIgniter\Model;
use ReflectionException;

class ResponsabiliteModel extends Model
{
    protected $table = 'responsabilite';

    protected $primaryKey = 'id_responsabilite';

    protected $returnType = ResponsabiliteEntity::class;

    protected $allowedFields = [
        'id_responsabilite',
        'libelle',
        'id_personne'
    ];

    public function __construct()
    {
        Parent::__construct();
    }

    public function deleteAll()
    {
        $responsabilites = $this->getAllResponsabilites();
        foreach ($responsabilites as $responsabilite) {
            $this->deleteResponsabilite($responsabilite['id_responsabilite']);
        }
    }

    /**
     * Fonction qui permet de retourner toutes les responsabilites
     * @return array
     */
    public function getAllResponsabilites(): array
    {
        return $this->findAll();
    }

    /**
     * Fonction qui supprimer une responsabilite
     * @param $id_responsabilite
     * @return bool|BaseResult
     */
    public function deleteResponsabilite($id_responsabilite)
    {
        return $this->where('id_responsabilite', $id_responsabilite)->delete();
    }

    /**
     * Fonction qui met à jour les informations d’une responsabilite
     */
    public function updateResponsabilite(int $id_responsabilite, array $data): bool
    {
        try {
            return $this->update($id_responsabilite, $data);
        } catch (ReflectionException $exception) {
            return false;
        }
    }

    /**
     * Fonction qui permet de retourner une responsabilite à partir de son id
     * @param int $id_responsabilite
     * @return array
     */
    public function getResponsabilite(int $id_responsabilite): array
    {
        return $this->where('id_responsabilite', $id_responsabilite)->find();
    }

    /**
     * Fonction qui ajoute une nouvelle responsabilite
     */
    public function insertResponsabilite(array $data): bool
    {
        try {
            return $this->insert($data);
        } catch (ReflectionException $exception) {
            return false;
        }
    }
}