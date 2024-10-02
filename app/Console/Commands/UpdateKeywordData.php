<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\KeywordDataUpdate;
use App\Models\Keyword;
use App\Models\Country;
use App\Models\CronStatus;
use App\Models\KeywordData;
use App\Traits\KeywordDaterange;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateKeywordData extends Command
{
    use KeywordDaterange;
    protected $cron;
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

        $this->cron = CronStatus::updateOrCreate(
            ['cron_name' => 'GSC Data Fetch', 'date' => Carbon::now()->format('Y-m-d')],
            ['status' => 2]
        );

        $keywords = Keyword::with(['keywordData' => function($query) {
            $query->with('country');
        }])->get();
        foreach($keywords as $keyword){
            foreach($keyword->keywordData as $data){
                $response = $this->keywordbydate($keyword, $data->country->ISO_CODE, $this->cron->id);
                $data['response'] = $response;
                $data->save();
            }
        }

        $status = $keywordDataUpdate->update($this->cron->id);
        if (!$status) {
            $this->cron->update([
                'status' => 0,
            ]);
            $this->info('Keyword metrics update failed.');
            return;
        }
        $this->cron->update([
            'status' => 1,
        ]);
        $this->info('Keyword metrics updated successfully.');
    }
}
