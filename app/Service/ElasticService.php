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
        return $client->search($data);
    }

    public function addElastic($id, $title, $auther, $createDate, $context)
    {
        // $params =[
        //     'index' => 'elastic' . date('YmdHms'),
        //     'type' => 'data',
        //     'id' => $id
        // ];
    
        // $params['body'] = [
        //     'title' => $title,
        //     'auther' => $auther,
        //     'createDate' => $createDate,
        // 	'context' => $context
        // ];

        $params = [
            'index' => 'my_index1',
            'id'    => 'my_id',
            'body'  => ['testField' => 'abc']
        ];
        
        $client = $this->connElastic();
        $response = $this->createElastic($client, $params);
        dd($response);
    }

    public function fuzzinSearch($search)
    {

        $params = [
            'index' => 'elastic20211101161113',
            'id' => '9',
        ];

        // $params = [
        //     'index' => 'my_index1',
        //     'id'    => 'my_id'
        // ];

        $params = [
            'index' => 'elastic20211101161144',
            'body'  => [
                'query' => [
                    // 'match' => [
                    //     'title' => '測試111'
                    // ]
                    'multi_match' => [
                        'query' => '測試',
                        'fuzziness' => 'AUTO',
                        'fields' => ['title', 'auther', 'content'],
                    ],
                ]
            ]
        ];

        $client = $this->connElastic();
        $response = $this->searchElastic($client, $params);

        $data = $response['hits']['hits'][0]['_source'];
         dd($data);
        
        return $data;
    }
    
}