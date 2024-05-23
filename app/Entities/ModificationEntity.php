<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class ModificationEntity extends Entity
{
    protected $casts = [
        'id_modification' => 'int',
        'id_personne' => 'int',
        'attribut' => 'string',
        'avant' => 'string',
        'apres' => 'string',
        'statut' => 'string',
        'commentaire' => 'string'
    ];
}