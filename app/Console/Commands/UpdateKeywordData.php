<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\KeywordDataUpdate;
use App\Models\Keyword;
use App\Models\Country;
use App\Models\KeywordData;
use App\Traits\KeywordDaterange;
use Illuminate\Support\Facades\Log;

class UpdateKeywordData extends Command
{
    use KeywordDaterange;
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
        $keywords = Keyword::with(['keywordData' => function($query) {
            $query->with('country');
        }])->get();

        foreach($keywords as $keyword){
            foreach($keyword->keywordData as $data){
                $response = $this->keywordbydate($keyword, $data->country->code);
                $data['response'] = $response;
                $data->save();
            }
        }

        $keywordDataUpdate->update();
        $this->info('Keyword metrics updated successfully.');
    }
}
