<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MeiliSearch\Client as MeiliSearch;

class SearchIndexCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:createIndex {index : The Index name of the Model} {--PK|primaryKey=id : The Index primary key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new MeiliSearch Index';

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
     * @return int
     */
    public function handle()
    {
        $index = $this->argument('index');
        $primaryKey = $this->option('primaryKey');

        $meilisearch = new MeiliSearch(config('meilisearch.host'), config('meilisearch.key'));

        $res = $meilisearch->createIndex($index, [
            'primaryKey' => $primaryKey
        ]);
        $this->info("The {$index} index creating status: {$res['status']}");
    }
}
