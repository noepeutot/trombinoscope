<?php

namespace App\Models;

use App\Entities\SejourEntity;
use CodeIgniter\Database\BaseResult;
use CodeIgniter\I18n\Time;
use CodeIgniter\Model;
use Exception;
use ReflectionException;

class SejourModel extends Model
{
    protected $table = 'sejour';

    protected $primaryKey = 'id_sejour';

    protected $returnType = SejourEntity::class;

    protected $dateFormat = 'date';

    protected $useTimestamps = false;

    protected $allowedFields = [
        'id_sejour',
        'date_debut',
        'date_fin',
        'id_personne',
        'sujet'
    ];

    protected $allowCallbacks = true;

    protected $afterFind = ['getFormatedDate'];


    public function __construct()
    {
        Parent::__construct();
    }

    /**
     * Fonction qui supprime tous les séjours
     * @return void
     */
    public function deleteAll()
    {
        $sejours = $this->getAllSejours();
        foreach ($sejours as $sejour) {
            $this->deleteSejour($sejour->id_sejour);
        }
    }

    /**
     * Fonction qui permet de retourner tous les séjours
     * @return array
     */
    public function getAllSejours(): array
    {
        return $this->findAll();
    }

    /**
     * Fonction qui supprimer un séjour
     * @param $id_sejour
     * @return bool|BaseResult
     */
    public function deleteSejour($id_sejour)
    {
        return $this->where('id_sejour', $id_sejour)
            ->delete();
    }

    /**
     * Fonction qui met à jour un séjour
     */
    public function updateSejour(int $id_sejour, array $data): bool
    {
        try {
            return $this->update($id_sejour, $data);
        } catch (ReflectionException $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Fonction qui permet de retourner un séjour à partir de son id
     * @param int $id_sejour
     * @return array|object|null
     */
    public function getSejour(int $id_sejour)
    {
        return $this->where('id_sejour', $id_sejour)
            ->find();
    }

    /**
     * Fonction qui permet de retourner le séjour d’une personne
     * @param int $id_personne
     * @return array|object|null
     */
    public function getSejourPersonne(int $id_personne)
    {
        return $this->where('id_personne', $id_personne)
            ->orderBy('date_fin', 'desc')
            ->first();
    }

    /**
     * Fonction qui ajoute un nouveau séjour
     */
    public function insertSejour(array $data): bool
    {
        try {
            return $this->insert($data);
        } catch (ReflectionException $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Fonction qui permet de retourner la date en base de données formatée de Y-m-d en d/m/Y
     * @throws Exception
     */
    protected function getFormatedDate(array $data): array
    {
        if(isset($data['data']->date_debut) && isset($data['data']->date_fin)) {
            $data['data']->date_debut = Time::createFromFormat('Y-m-d', $data['data']->date_debut)->format('d/m/Y');
            $data['data']->date_fin = Time::createFromFormat('Y-m-d', $data['data']->date_fin)->format('d/m/Y');
        }
        return $data;
    }
}