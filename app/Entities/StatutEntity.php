<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class StatutEntity extends Entity
{
    protected $casts = [
        'id_statut' => 'int',
        'statut' => 'string'
    ];
}