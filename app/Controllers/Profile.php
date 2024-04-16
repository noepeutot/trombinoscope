<?php

namespace App\Controllers;

class Profile extends BaseController
{
    public function index($id) {
        $data['id'] = [$id];

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

        return view('profile', $data);
    }
}