<?php

namespace App\Console\Commands;

use App\Jobs\SendDailySalesReport;
use Illuminate\Console\Command;

class GenerateDailySalesReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales:generate-daily-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and send daily sales report to admin';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Generating daily sales report...');

        SendDailySalesReport::dispatch();

        $this->info('Daily sales report job has been queued.');

        return self::SUCCESS;
    }
}
