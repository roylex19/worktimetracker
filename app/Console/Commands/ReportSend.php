<?php

namespace App\Console\Commands;

use App\Mail\WorktimeReport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ReportSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:send';

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

    public function handle()
    {
        Mail::to([
            'mail1@qwe.ru',
            'mail2@qwe.ru'
        ])->send(new WorktimeReport());
    }
}
