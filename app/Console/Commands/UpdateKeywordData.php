<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\KeywordDataUpdate;
use Illuminate\Support\Facades\Log;

class UpdateKeywordData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'keywords:update-metrics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(KeywordDataUpdate $keywordDataUpdate)
    {
        $keywordDataUpdate->update();
        $this->info('Keyword metrics updated successfully.');
    }
}
