<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class EncadrantEntity extends Entity
{
    protected $casts = [
        'id_encadrant' => 'int',
        'id_sejour' => 'int',
        'id_personne' => 'int',
        'nom' => 'string',
        'prenom' => 'string'
    ];
}