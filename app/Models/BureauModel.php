<?php

namespace App\Models;

use App\Entities\BureauEntity;
use CodeIgniter\Database\BaseResult;
use CodeIgniter\Model;
use ReflectionException;

class BureauModel extends Model
{
    protected $table = 'bureau';

    protected $primaryKey = 'id_bureau';

    protected $returnType = BureauEntity::class;

    protected $allowedFields = [
        'id_bureau',
        'numero',
    ];

    public function __construct()
    {
        Parent::__construct();
    }

    /**
     * Fonction qui supprime tous les bureaux
     * @return void
     */
    public function deleteAll()
    {
        $bureaux = $this->getAllBureaux();
        foreach ($bureaux as $bureau) {
            $this->deletebureau($bureau->id_bureau);
        }
    }

    /**
     * Fonction qui permet de retourner tous les bureaux
     * @return array
     */
    public function getAllBureaux(): array
    {
        return $this->findAll();
    }

    /**
     * Fonction qui supprimer un bureau
     * @param int $id_bureau
     * @return bool|BaseResult
     */
    public function deleteBureau(int $id_bureau)
    {
        return $this->where('id_bureau', $id_bureau)->delete();
    }

    /**
     * Fonction qui met à jour un bureau
     */
    public function updateBureau(int $id_bureau, array $data): bool
    {
        try {
            return $this->update($id_bureau, $data);
        } catch (ReflectionException $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Fonction qui permet de retourner un bureau à partir de son id
     * @param int $id_bureau
     * @return array|object|null
     */
    public function getBureau(int $id_bureau)
    {
        return $this->where('id_bureau', $id_bureau)->first();
    }

    /**
     * Fonction qui permet de retourner le bureau d’une personne à partir de son id
     * @param int $id_personne
     * @return array|object|null
     */
    public function getBureauPersonne(int $id_personne)
    {
        return $this->where('id_bureau IN 
        (SELECT b.id_bureau
        FROM bureau b, personne p 
        WHERE b.id_bureau=p.bureau
        AND p.id_personne='. $id_personne.')')->first();
    }

    /**
     * Fonction qui ajoute un nouveau bureau
     */
    public function insertBureau(array $data): bool
    {
        try {
            return $this->insert($data);
        } catch (ReflectionException $exception) {
            return $exception->getMessage();
        }
    }
}