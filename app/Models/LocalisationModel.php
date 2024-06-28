<?php

namespace App\Models;

use CodeIgniter\Database\BaseResult;
use CodeIgniter\Model;
use ReflectionException;

class LocalisationModel extends Model
{
    protected $table = 'localisation';

    protected $primaryKey = 'id_localisation';

    protected $returnType = LocalisationModel::class;

    protected $allowedFields = [
        'id_localisation',
        'telephone',
        'bureau',
        'sejour',
        'personne'
    ];

    public function __construct()
    {
        Parent::__construct();
    }

    /**
     * Fonction qui supprime toutes les localisations
     * @return void
     */
    public function deleteAll()
    {
        $localisations = $this->getAllLocalisations();
        foreach ($localisations as $localisation) {
            $this->deleteLocalisation($localisation->id_localisation);
        }
    }

    /**
     * Fonction qui permet de retourner toutes les localisations
     * @return array
     */
    public function getAllLocalisations(): array
    {
        return $this->findAll();
    }

    /**
     * Fonction qui supprimer un localisation
     * @param $id_localisation
     * @return bool|BaseResult
     */
    public function deleteLocalisation($id_localisation)
    {
        return $this->where('id_localisation', $id_localisation)->delete();
    }

    /**
     * Fonction qui met à jour une localisation
     */
    public function updateLocalisation(int $id_localisation, array $data): bool
    {
        try {
            return $this->update($id_localisation, $data);
        } catch (ReflectionException $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Fonction qui permet de retourner une localisation à partir de son id
     * @param int $id_localisation
     * @return array|object|null
     */
    public function getlocalisation(int $id_localisation)
    {
        return $this->where('id_localisation', $id_localisation)->first();
    }

    /**
     * Fonction qui permet de retourner les localisations d'une personne à partir de son id
     * @param int $id_personne
     * @return array|null
     */
    public function getLocalisationsPersonne(int $id_personne): ?array
    {
        return $this->where('personne', $id_personne)
            ->find();
    }

    /**
     * Fonction qui ajoute une nouvelle localisation
     */
    public function insertLocalisation(array $data): bool
    {
        try {
            return $this->insert($data);
        } catch (ReflectionException $exception) {
            return $exception->getMessage();
        }
    }
}