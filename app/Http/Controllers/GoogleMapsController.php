<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GoogleMapsController extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function autocomplete(Request $request)
    {
        return \GoogleMaps::load('placeautocomplete')
            ->setParamByKey('input', $request->query('input'))
            ->setParamByKey('language', 'pt-BR')
            ->setParamByKey('components', 'country:br')
            ->setParamByKey('types', 'address')
            ->getResponseByKey('predictions');
    }
}
