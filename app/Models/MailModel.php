<?php

namespace App\Models;

use App\Entities\MailEntity;
use CodeIgniter\Database\BaseResult;
use CodeIgniter\Model;
use ReflectionException;

class MailModel extends Model
{
    protected $table = 'mail';

    protected $primaryKey = 'id_mail';

    protected $returnType = MailEntity::class;

    protected $allowedFields = [
        'id_mail',
        'libelle',
        'type',
        'id_personne'
    ];

    public function __construct()
    {
        Parent::__construct();
    }

    /**
     * Fonction qui supprime tous les mails
     * @return void
     */
    public function deleteAll()
    {
        $mails = $this->getAllMails();
        foreach ($mails as $mail) {
            $this->deleteMail($mail->id_mail);
        }
    }

    /**
     * Fonction qui permet de retourner tous les mails
     * @return array
     */
    public function getAllMails(): array
    {
        return $this->findAll();
    }

    /**
     * Fonction qui supprimer un mail
     * @param $id_mail
     * @return bool|BaseResult
     */
    public function deleteMail($id_mail)
    {
        return $this->where('id_mail', $id_mail)->delete();
    }

    /**
     * Fonction qui met à jour un mail
     */
    public function updateMail(int $id_mail, array $data): bool
    {
        try {
            return $this->update($id_mail, $data);
        } catch (ReflectionException $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Fonction qui permet de retourner un mail à partir de son id
     * @param int $id_mail
     * @return array
     */
    public function getMail(int $id_mail): array
    {
        return $this->where('id_mail', $id_mail)->find();
    }

    /**
     * Fonction qui permet de retourner les mails d’une personne
     * @param int $personneID
     * @return array|object|null
     */
    public function getMailPersonne(int $personneID)
    {
        return $this->where('id_personne', $personneID)
            ->whereNotIn('type', ['perso'])->first();
    }

    /**
     * Fonction qui ajoute un nouveau mail
     */
    public function insertMail(array $data): bool
    {
        try {
            return $this->insert($data);
        } catch (ReflectionException $exception) {
            return $exception->getMessage();
        }
    }
}