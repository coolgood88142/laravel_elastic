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

    public function addElastic($id, $title, $author, $createDate, $context)
    {
        $params =[
            'index' => 'elastic',
            'id' => $id
        ];
    
        // $params['body'] = [
        //     'title' => [
        //         'type' => 'text',
        //         'value' => $title
        //     ],
        //     'author' => [
        //         'type' => 'text',
        //         'value' => $author
        //     ],
        //     'createDate' => [
        //         'type' => 'date',
        //         'value' => $createDate
        //     ],
        // 	'context' => [
        //         'type' => 'text',
        //         'value' => $context
        //     ],
        // ];

        
        $params['body'] = [
            'name'=> [
                "type"=> "text"
            ],
            "blob"=> [
                "type"=> "binary"
            ],
            "is_published"=> [
                "type"=> "boolean"
            ],
            "tags"=> [
                "type"=>  "keyword"
            ],
            "number_of_bytes"=> [
                "type"=> "integer"
            ],
            "time_in_seconds"=> [
                "type"=> "float"
            ],
            "price"=> [
                "type"=> "scaled_float",
                "scaling_factor"=> 100
            ],
            "create_date"=> [
                "type"=> "date"
            ],
            "distance"=> [
                "type"=> "long"
            ],
            "route_length_miles"=> [
                "type"=> "alias",
                "path"=> "distance" 
            ],
            "title"=> [
                "type"=> "text"
            ],
            "labels"=> [
                "type"=> "flattened"
            ],
            "user"=> [
                "type"=> "nested" 
            ],
            "my_join_field"=> [ 
                "type"=> "join",
                "relations"=> [
                "question"=> "answer" 
                ]
            ],
            "expected_attendees"=> [
                "type"=> "integer_range"
            ],
            "ip_addr"=> [
                "type"=> "ip"
            ],
            "my_version"=> [
                "type"=> "version"
            ],
            "my-agg-metric-field"=> [
                "type"=> "aggregate_metric_double",
                "metrics"=> [ "min", "max", "sum", "value_count" ],
                "default_metric"=> "max"
            ],
            "my_histogram" => [
                "type" => "histogram"
            ],
            "my_dense_vector"=> [
                "type"=> "dense_vector",
                "dims"=> 3  
            ],
            "my_sparse_vector"=> [
                "type"=> "sparse_vector"
            ],
            "pagerank"=> [
                "type"=> "rank_feature" 
            ],
            "topics"=> [
                "type"=> "rank_features" 
            ],
            "location1"=> [
                "type"=> "geo_point"
            ],
            "longitude1"=> [
                "type"=> "geo_shape"
            ],
            "location2"=> [
                "type"=> "point"
            ],
            "longitude2"=> [
                "type"=> "shape"
            ],
            "query"=> [
                "type"=> "percolator"
            ]
        ];


        // $params = [
        //     'index' => 'my_index1',
        //     'id'    => 'my_id',
        //     'body'  => ['testField' => 'abc']
        // ];
        
        $client = $this->connElastic();
        $response = $this->createElastic($client, $params);
    }

    public function fuzzySearch($search)
    {

        // $params = [
        //     'index' => 'elastic20211101161113',
        //     'id' => '9',
        // ];

        // $params = [
        //     'index' => 'my_index1',
        //     'id'    => 'my_id'
        // ];

        $params = [
            'index' => config('scout.elasticsearch.index'),
            'body' => [
                'sort' => [
                    'createDate' => [
                        "order" => "desc"
                    ]
                ]
            ]
        ];

        if($search != '' && $search != null){
            $params['body'] = [
                'query' => [
                    // 'match' => [
                    //     'title' => '測試111'
                    // ]
                    'multi_match' => [
                        'query' => $search,
                        'fuzziness' => 'AUTO',
                        'fields' => ['title', 'author', 'content'],
                    ],
                ]
            ];
        }

        

        $client = $this->connElastic();
        $response = $this->searchElastic($client, $params);
        $data = $response['hits']['hits'];
        
        return $data;
    }
    
}