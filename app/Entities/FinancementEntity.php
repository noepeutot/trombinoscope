<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class FinancementEntity extends Entity
{
    protected $casts = [
        'id_financement' => 'int',
        'id_sejour' => 'int',
        'id_employeur' => 'int',
    ];
}