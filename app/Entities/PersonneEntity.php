<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class PersonneEntity extends Entity
{
    protected $casts = [
        'id_personne' => 'int',
        'login' => 'string',
        'role' => 'string',
        'nom' => 'string',
        'prenom' => 'string',
        'telephone' => 'string',
        'statut' => 'string',
        'bureau' => 'string'
    ];
}