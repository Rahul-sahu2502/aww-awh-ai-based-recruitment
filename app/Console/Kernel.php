<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            $url = 'https://aww.e-bharti.in/admin/load-api-data';
            try {
                $response = Http::timeout(60)->get($url);
                Log::info('Load API scheduler hit', [
                    'url' => $url,
                    'status' => $response->status(),
                    'ok' => $response->successful(),
                ]);
            } catch (\Throwable $e) {
                Log::error('Load API scheduler failed', [
                    'url' => $url,
                    'error' => $e->getMessage(),
                ]);
            }
        })
            ->name('load-api-data-daily')
            ->dailyAt('01:15')
            ->timezone('Asia/Kolkata')
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
