<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MeiliSearch\Client as MeiliSearch;

class SearchSettingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:sync {model? : Class name of model to update settings} {index? : The Index name of the Model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync model configuration with MeiliSearch';

    /**
     * @var MeiliSearch
     */
    private MeiliSearch $meilisearch;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->meilisearch = new MeiliSearch(config('meilisearch.host'), config('meilisearch.key'));
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($class = $this->argument('model')) {
            $model = new $class;
            $this->syncModel($model);
            return 1;
        }
        $this->syncAll();

        return 1;
    }

    /**
     * @param $model
     * @return void
     */
    private function syncModel($model): void
    {
        if ($this->hasSettings($model)) {
            $this->updateSettings($model);
        }
    }

    /**
     * @return void
     */
    private function syncAll(): void
    {
        collect(scandir(app_path('Models')))->each(
            function ($path) {
                if ($path === '.' || $path === '..') {
                    return true;
                }

                $class = 'App\Models\\' . substr($path, 0, -4);
                $model = new $class;
                $this->syncModel($model);
            }
        );
    }

    /**
     * @param $model
     * @return void
     */
    private function updateSettings($model): void
    {
        if ($indexName = $this->argument('index')) {
            $index = $this->meilisearch->index($indexName);
        } elseif ($this->hasIndex($model)) {
            $index = $this->meilisearch->index($model->meiliSearchIndex);
        } else {
            $index = null;
        }

        if ($index) {
            collect($model->meiliSearchSettings)->each(
                function ($value, $key) use ($index) {
                    $status = $index->{$key}($value);
                    $this->line("{$key} has been updated, indexUid: {$status['indexUid']}");
                }
            );
        }
    }

    /**
     * @param $model
     * @return bool
     */
    private function hasIndex($model): bool
    {
        return property_exists($model, 'meiliSearchIndex');
    }

    /**
     * @param $model
     * @return bool
     */
    private function hasSettings($model): bool
    {
        return property_exists($model, 'meiliSearchSettings');
    }
}
