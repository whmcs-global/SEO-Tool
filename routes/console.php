<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('keywords:update-metrics')->daily()->at('00:00');
Schedule::command('keyword:fetch-new-keyword')->daily()->at('01:00');
