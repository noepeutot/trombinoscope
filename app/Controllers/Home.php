<?php

namespace App\Controllers;

class Home extends BaseController
{
    /**
     * @return string
     */
    public function index(): string
    {
        $apiKey = 'blablabla';
        $headers = array(
            'Content-Type:application/json',
            'Authorization: ' . $apiKey
        );
        $request = curl_init('http://localhost:8081/personnels');
        curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
        // Enlever le header de la réponse
        curl_setopt($request, CURLOPT_HEADER, FALSE);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, TRUE);
        $resultat = curl_exec($request);
        curl_close($request);

        $data['statut'] = ['Chercheur', 'Doctorant', 'Enseignant-Chercheur'];
        $data['equipe'] = ['MAGE', 'MADEO', 'MADEA', 'SYREL'];
        $data['tuteur'] = ['COUSTEAU Eric', 'POLLET Colette', 'POLIZZI Rachelle', 'ARRIEULA Beatrice'];
        $data['personnels'] = json_decode($resultat, TRUE);

        return view('home', $data);
    }

    /**
     * Function qui permet la recherche
     * @param $query
     * @return void
     */
    public function search($query) {

    }
}
