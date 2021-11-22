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
                "type"=> "text",
                "value"=>"王小明"
            ],
            "blob"=> [
                "type"=> "binary",
                "value"=> "test1234"
            ],
            "is_published"=> [
                "type"=> "boolean",
                "value"=>true
            ],
            "tags"=> [
                "type"=>  "keyword",
                "value"=> "articles"
            ],
            
            "create_date"=> [
                "type"=> "date",
                "value"=> '2021-11-23'
            ],
            "distance"=> [
                "type"=> "long",
                "value" => 123345567789
            ],
            "title"=> [
                "type"=> "text",
                "value"=> "標題"
            ],
            "labels"=> [
                "type"=> "flattened",
                "value"=> "testFlattened" 
            ],
            "user"=> [
                "type"=> "nested",
                "value"=> "testNested"  
            ],
            "my_join_field"=> [ 
                "type"=> "join",
                "relations"=> [
                    "value"=> "answer" 
                ]
            ],
            "expected_attendees"=> [
                "type"=> "integer_range",
                "value"=> "testRange" 
            ],
            "ip_addr"=> [
                "type"=> "ip",
                "value"=> "127.0.0.1" 
            ],
            "my_version"=> [
                "type"=> "version",
                "value"=> "testVersion"
            ],
            "my-agg-metric-field"=> [
                "type"=> "aggregate_metric_double",
                "metrics"=> [ "min", "max", "sum", "value_count" ],
                "default_metric"=> "max",
                "value"=> "testAggregateMetricDouble"
            ],
            "my_histogram" => [
                "type" => "histogram",
                "value"=> "testHistogram"
            ],
            "my_dense_vector"=> [
                "type"=> "dense_vector",
                "dims"=> 3,
                "value"=> "testDenseVector"  
            ],
            "my_sparse_vector"=> [
                "type"=> "sparse_vector",
                "value"=> "testSparseVector"  
            ],
            "pagerank"=> [
                "type"=> "rank_feature",
                "value"=> "testRankFeature"  
            ],
            "location1"=> [
                "type"=> "geo_point",
                "value"=> 41.12
            ],
            "longitude1"=> [
                "type"=> "geo_shape",
                "value"=> -71.34  
            ],
            "location2"=> [
                "type"=> "point",
                "value"=> [
                    'x' => 41.12,
                    'y' => -71.34
                ] 
            ],
            "longitude2"=> [
                "type"=> "shape",
                "value" => [-377.03653, 389.897676]
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