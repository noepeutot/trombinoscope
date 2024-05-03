<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class ResponsabiliteEntity extends Entity
{
    protected $casts = [
        'id_responsabilite' => 'int',
        'libelle' => 'string',
        'id_personne' => 'int'
    ];
}