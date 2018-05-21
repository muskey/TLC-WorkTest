<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends Controller {

    public function convert() {

        $response = [];
        
        $request = Request::createFromGlobals();
        $quantity = $request->request->get('quantity', 0);
        $from = $request->request->get('from', 'THB');
        $to = $request->request->get('to', 'USD');


        $response['quantity'] = $quantity;
        $response['from'] = $from;
        $response['to'] = $to;
        $response['data'] =  ['value' => 0];

        try {
            
            $client = new \OneForge\ForexQuotes\ForexDataClient(getenv('API_KEY', NULL));
            $data = $client->convert($from, $to, $quantity);
            $response['data'] = $data;
            
        } catch (\Exception $ex) {
            $url = 'https://forex.1forge.com/1.0.3/convert?from='.$from.'&to='.$to.'&quantity='.$quantity.'&api_key='.getenv('API_KEY', NULL);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL,$url);
            $data = curl_exec($ch);
            curl_close($ch);
            
            $response['data'] = json_decode($data);
        }

        return new \Symfony\Component\HttpFoundation\JsonResponse($response);
    }

}
