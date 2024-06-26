<?php

namespace App\Models;

use App\Entities\ModificationEntity;
use CodeIgniter\Database\BaseResult;
use CodeIgniter\Model;
use ReflectionException;

class ModificationModel extends Model
{
    protected $table = 'modification';

    protected $primaryKey = 'id_modification';

    protected $returnType = ModificationEntity::class;

    protected $allowedFields = [
        'id_modification',
        'id_personne',
        'attribut',
        'avant',
        'apres',
        'statut',
        'commentaire'
    ];

    protected $beforeFind = ['getData'];

    public function __construct()
    {
        Parent::__construct();
    }

    /**
     * Fonction qui supprime toutes les modifications
     * @return void
     */
    public function deleteAll()
    {
        $modifications = $this->getAllmodifications();
        foreach ($modifications as $modification) {
            $this->deleteModification($modification->id_modification);
        }
    }

    /**
     * Fonction qui permet de retourner toutes les modifications
     * @return array
     */
    public function getAllModifications(): array
    {
        return $this->findAll();
    }

    /**
     * Fonction qui supprimer une modification
     * @param $id_modification
     * @return bool|BaseResult
     */
    public function deleteModification($id_modification)
    {
        return $this->where('id_modification', $id_modification)->delete();
    }

    /**
     * Fonction qui retourne un certain nombre des dernières modifications
     * @param int $nombreLimite
     * @return array|null|object
     */
    public function getModificationRecente(int $nombreLimite)
    {
        return $this->orderBy('id_modification', 'DESC')
            ->limit($nombreLimite)
            ->find();
    }

    /**
     * Fonction qui retourne un certain nombre des dernières modifications en attente
     * @param int $nombreLimite
     * @return array|null|object
     */
    public function getModificationEnAttenteRecente(int $nombreLimite)
    {
        $this->where('statut', 'attente')
            ->orderBy('id_modification', 'DESC');
        if ($nombreLimite != -1) {
            $this->limit($nombreLimite);
        }
        return $this->find();
    }

    /**
     * Fonction qui retourne le nombre de modifications en attente
     * @return int|string
     */
    public function countModificationEnAttente()
    {
        return $this->where('statut', 'attente')
            ->countAllResults();
    }

    /**
     * Fonction qui retourne le nombre de modifications déjà validées ou refusées
     * @return int|string
     */
    public function countModificationHistorique()
    {
        return $this->whereNotIn('statut', ['attente'])
            ->countAllResults();
    }

    /**
     * Fonction qui retourne un certain nombre des dernières modifications déjà validées ou refusées
     * @param int $nombreLimite
     * @return array|null|object
     */
    public function getModificationHistoriqueRecente(int $nombreLimite)
    {
        $this->whereNotIn('statut', ['attente'])
            ->orderBy('id_modification', 'DESC');
        if ($nombreLimite != -1) {
            $this->limit($nombreLimite);
        }
        return $this->find();
    }

    /**
     * Fonction qui met à jour une modification
     * @param int $id_modification
     * @param array $data
     * @return bool
     */
    public function updateModification(int $id_modification, array $data): bool
    {
        try {
            return $this->update($id_modification, $data);
        } catch (ReflectionException $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Fonction qui permet de retourner une modification à partir de son id
     * @param int $id_modification
     * @return array|object|null
     */
    public function getModification(int $id_modification)
    {
        return $this->where('id_modification', $id_modification)->first();
    }

    /**
     * Fonction qui permet de retourner les modifications d’une personne
     * @param int $personneID
     * @return array|object|null
     */
    public function getModificationsPersonne(int $personneID)
    {
        return $this->where('id_personne', $personneID)
            ->find();
    }

    /**
     * Fonction qui retourne la modification d'une personne d'un attribut en particulier
     * @param int $personneID
     * @param string $attribut
     * @return array|object|null
     */
    public function getModificationAttentePersonneAttribut(int $personneID, string $attribut)
    {
        return $this->where('id_personne', $personneID)
            ->where('attribut', $attribut)
            ->where('statut', 'attente')
            ->first();
    }

    /**
     * Fonction qui ajoute une nouvelle modification
     */
    public function insertModification(array $data): bool
    {
        try {
            return $this->insert($data);
        } catch (ReflectionException $exception) {
            return $exception->getMessage();
        }
    }

    public function getData($data)
    {
        if (isset($data['data']->attribut) && ($data['data']->attribut == 'bureau' || $data['data']->attribut == 'categorie')) {
            $data['data']->avant = intval($data['data']->avant);
            $data['data']->apres = intval($data['data']->apres);
        } else if (isset($data['data']->attribut) && ($data['data']->attribut == 'equipe' || $data['data']->attribut == 'employeur')) {
            $listData = [];
            if (!empty($data['data']->apres)) {
                $listID = explode(', ', $data['data']->apres);
                foreach ($listID as $ID) {
                    $listData[] = intval($ID);
                }
            }
            $data['data']->apres = $listData;
        }
        return $data;
    }
}