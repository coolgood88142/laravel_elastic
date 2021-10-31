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

    public function searchElastic($client, $data)
    {
        $response = $client->search($data);
    }

    public function addElastic($id, $title, $auther, $createDate, $context)
    {
        $params =[
            'index' => 'elastic' . date('YmdHms'),
            'type' => 'data',
            'id' => $id
        ];
    
        $params['body'] = [
            'title' => $title,
            'auther' => $auther,
            'createDate' => $createDate,
        	'context' => $context
        ];

        
    
        $client = $this->connElastic();
        $response = $this->createElastic($client, $params);
        dd($response);
    }

    public function fuzzinSearch($search)
    {
        // $query = [
        //     'multi_match' => [
        //         'query' => $search,
        //         'fuzziness' => 'AUTO',
        //         'fields' => ['title', 'auther', 'content'],
        //     ],
        // ];

        // $query = [
        //     'fuzzy'=>[
        //         "title.keyword" => [
        //             "value" => $search
        //         ],
        //     ],
        // ];

        $query = [
            'match'=>[
                "title" => $search,
            ],
        ];

        $params = [
            'index' => 'elastic20211029191024',
            'type' => 'text',
            'body' => [
                'query' => $query
            ]
        ];

        // dd($params);

        $client = $this->connElastic();
        $response = $client->indices()->getMapping();
        // $response = $this->searchElastic($client, $params);
        // $params = ['type' => 'text'];
        // $response = $client->indices()->getMapping($params);
        dd($response);
    }
    
}