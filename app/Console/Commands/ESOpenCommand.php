<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ElasticService;

class ESOpenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $host = config('scout.elasticsearch.hosts');
        $index = config('scout.elasticsearch.index');
        $elasticService = new ElasticService();
        $client = $elasticService->connElastic();

        if ($client->indices()->exists(['index' => $index])) {
            $client->indices()->delete(['index' => $index]);
        }

        return $client->indices()->create([
            'index' => $index,
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0
                ],
                'mappings' => [
                    '_source' => [
                        'enabled' => true
                    ],
                    'properties' => [
                        'title' => [
                            'type' => 'text',
                            'analyzer' => 'ik_max_word',
                            'search_analyzer' => 'ik_smart'
                        ],
                        'author' => [
                            'type' => 'text',
                            'analyzer' => 'ik_max_word',
                            'search_analyzer' => 'ik_smart'
                        ],
                        'content' => [
                            'type' => 'text',
                            'analyzer' => 'ik_max_word',
                            'search_analyzer' => 'ik_smart'
                        ],
                        'create_date' => [
                            'type' => 'date'
                        ],
                    ],
                ]
            ]
        ]);
    }
}
