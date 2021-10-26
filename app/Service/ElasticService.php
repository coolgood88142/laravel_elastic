<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use Elasticsearch\ClientBuilder;
use Config;

class ElasticService
{
    public function connElastic()
    {
        $elastic = Config::get('elastic');

        $client = ClientBuilder::create()->setHosts($elastic['hosts'])->build();

        return $client;
    }

    public function createElastic($client, $data)
    {
        $response = $client->create($data);
    }

    public function updateElastic($client, $data)
    {
        $response = $client->update($data);
    }

    public function deleteElastic($client, $data)
    {
        $response = $client->delete($data);
    }

    public function addElastic($title, $auther, $createDate, $context)
    {
        $params =[
            'index' => date('YmdHms'),
            'type' => 'data',
            'id' => 'el1'
        ];
    
        $params['body'] = [
            'title' => $title,
            'auther' => $auther,
            'createDate' => $createDate,
        	'context' => $context
        ];
    
        $client = $this->connElastic();
        $this->createElastic($client, $params);
    }
}