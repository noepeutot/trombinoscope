<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class BureauEntity extends Entity
{
    protected $casts = [
        'id_bureau' => 'int',
        'numero' => 'string'
    ];
}