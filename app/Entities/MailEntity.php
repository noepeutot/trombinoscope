<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class MailEntity extends Entity
{
    protected $casts = [
        'id_mail' => 'int',
        'libelle' => 'string',
        'type' => 'string',
        'id_personne' => 'int'
    ];
}