<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class PersonneEntity extends Entity
{
    protected $casts = [
        'id_personne' => 'int',
        'nom' => 'string',
        'prenom' => 'string',
        'telephone' => 'string',
        'statut' => 'string',
        'equipe' => 'string',
        'numero_bureau' => 'string'
    ];
}