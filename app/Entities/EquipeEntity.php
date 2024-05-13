<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class EquipeEntity extends Entity
{
    protected $casts = [
        'id_equipe' => 'int',
        'nom_court' => 'string',
        'nom_long' => 'string'
    ];
}