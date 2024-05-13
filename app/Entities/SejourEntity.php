<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class SejourEntity extends Entity
{
    protected $casts = [
        'id_sejour' => 'int',
        'date_debut' => 'date',
        'date_fin' => 'date',
        'id_personne' => 'int',
        'sujet' => 'string'
    ];


}