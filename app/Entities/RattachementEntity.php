<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class RattachementEntity extends Entity
{
 protected $casts = [
     'id_rattachement' => 'int',
     'id_sejour' => 'string',
     'id_' => 'string'
 ];
}