<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class LocalisationEntity extends Entity
{
 protected $casts = [
     'id_localisation' => 'int',
     'telephone' => 'string',
     'bureau' => 'int',
     'sejour' => 'int',
     'personne' => 'int'
 ];
}