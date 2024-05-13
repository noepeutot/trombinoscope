<?php

namespace App\Models;

use App\Entities\EquipeEntity;
use CodeIgniter\Database\BaseResult;
use CodeIgniter\Model;
use ReflectionException;

class EquipeModel extends Model
{
    protected $table = 'equipe';

    protected $primaryKey = 'id_equipe';

    protected $returnType = EquipeEntity::class;

    protected $allowedFields = [
        'id_equipe',
        'nom_court',
        'nom_long'
    ];

    public function __construct()
    {
        Parent::__construct();
    }

    /**
     * Fonction qui supprime toutes les équipes
     * @return void
     */
    public function deleteAll()
    {
        $equipes = $this->getAllEquipes();
        foreach ($equipes as $equipe) {
            $this->deleteEquipe($equipe->id_equipe);
        }
    }

    /**
     * Fonction qui permet de retourner toutes les équipes
     * @return array
     */
    public function getAllEquipes(): array
    {
        return $this->findAll();
    }

    /**
     * Fonction qui supprimer une équipe
     * @param $id_equipe
     * @return bool|BaseResult
     */
    public function deleteEquipe($id_equipe)
    {
        return $this->where('id_equipe', $id_equipe)->delete();
    }

    /**
     * Fonction qui met à jour une équipe
     */
    public function updateEquipe(int $id_equipe, array $data): bool
    {
        try {
            return $this->update($id_equipe, $data);
        } catch (ReflectionException $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Fonction qui permet de retourner une équipe à partir de son id
     * @param int $id_equipe
     * @return array
     */
    public function getEquipe(int $id_equipe): array
    {
        return $this->where('id_equipe', $id_equipe)->find();
    }

    /**
     * Fonction qui ajoute une nouvelle équipe
     */
    public function insertEquipe(array $data): bool
    {
        try {
            return $this->insert($data);
        } catch (ReflectionException $exception) {
            return $exception->getMessage();
        }
    }
}