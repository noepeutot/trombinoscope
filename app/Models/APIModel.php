<?php

namespace App\Models;

use CodeIgniter\Model;

class APIModel extends Model
{
    protected string $apiKey = 'blablabla';

    protected string $urlBase = 'http://localhost:8081/';

    public function __construct()
    {
        Parent::__construct();
        $this->apiKey = 'blablabla';
        $this->urlBase = 'http://localhost:8081/';
    }

    /**
     * Fonction qui permet de faire une requête à l'API sur la route donnée.
     * @param $url
     * @return mixed JSON décodé
     */
    public function getDataFromURL($url)
    {
        $headers = array(
            'Content-Type:application/json',
            'Authorization: ' . $this->apiKey
        );
        $request = curl_init($this->urlBase . $url);
        curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
        // Enlever le header de la réponse
        curl_setopt($request, CURLOPT_HEADER, FALSE);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, TRUE);
        $resultat = curl_exec($request);
        curl_close($request);
        $resultat = json_decode($resultat, TRUE);
        return $resultat;
    }
}