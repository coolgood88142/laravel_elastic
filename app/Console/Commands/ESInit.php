<?php
namespace App\Console\Commands;

use App\Services\ElasticService;
use Illuminate\Console\Command;
use GuzzleHttp\Client;

class ESInit extends Command {

    protected $signature = 'es:init';
    protected $description = 'init laravel es for news';
    protected $elasticService;

    public function __construct() { parent::__construct(); }

    public function handle() {

        $client = new Client();

        $url = config('scout.elasticsearch.hosts')[0] . ':9200' . '/_template/news';

        $params = [
            'json' => [
                'template' => config('scout.elasticsearch.index'),
                'settings' => [
                    'number_of_shards' => 5
                ],
                'mappings' => [
                    '_default_' => [
                        '_all' => [
                            'enabled' => true
                        ],
                        'dynamic_templates' => [
                            [
                                'strings' => [
                                    'match_mapping_type' => 'string',
                                    'mapping' => [
                                        'type' => 'text',
                                        'analyzer' => 'ik_smart',
                                        'ignore_above' => 256,
                                        'fields' => [
                                            'keyword' => [
                                                'type' => 'keyword'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $client->put($url, $params);

        // 创建index
        $url = config('scout.elasticsearch.hosts')[0] . ':9200' . '/' . config('scout.elasticsearch.index');

        $params = [
            'json' => [
                'settings' => [
                    'refresh_interval' => '5s',
                    'number_of_shards' => 5,
                    'number_of_replicas' => 0
                ],

                'mappings' => [
                    '_default_' => [
                        '_all' => [
                            'enabled' => false
                        ]
                    ]
                ]
            ]
        ];

        $client->put($url, $params);

    }

}