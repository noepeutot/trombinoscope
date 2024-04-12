<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $data['statut'] = ['Chercheur', 'Doctorant', 'Technicien'];
        $data['equipe'] = ['MAGE', 'MADEO', 'MADEA', 'SYREL'];
        $data['tuteur'] = ['COUSTEAU Eric', 'POLLET Colette', 'POLIZZI Rachelle', 'ARRIEULA Beatrice'];
        $data['personnels'] = array(1 => ["nom" => "Jean", "prénom" => "Paul"], 2 => ["nom" => "Gaspar", "prénom" => "Cousteau"]);
        return view('home', $data);
    }
}
