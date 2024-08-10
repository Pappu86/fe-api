<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MeiliSearch\Client as MeiliSearch;

class SearchIndexDeleteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:deleteIndex {index : The Index name of the Model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a MeiliSearch Index';

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

        $meilisearch = new MeiliSearch(config('meilisearch.host'), config('meilisearch.key'));

        $res = $meilisearch->deleteIndex($index);
        $this->info("The {$index} index deleting status: {$res['status']}");
    }
}
