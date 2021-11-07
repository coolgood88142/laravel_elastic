<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Articles;

class ElasearchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:search {query}';

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
        $articles = Articles::search($this->argument('query'))->get();
        $this->info($articles->title);
    }
}
