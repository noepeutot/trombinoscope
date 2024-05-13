<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class EmployeurEntity extends Entity
{
    protected $casts = [
        'id_employeur' => 'int',
        'nom' => 'string',
        'nom_court' => 'string'
    ];
}