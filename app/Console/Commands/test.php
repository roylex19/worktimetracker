<?php

namespace App\Console\Commands;

use App\Project;
use Illuminate\Console\Command;

class test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:start';

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
        $r = Project::pluck('title')->toArray();

        for ($i = 0; $i < 1000; $i++) {
            for ($j = 0; $j < 1000; $j++) {
                $project = sprintf("%'03d", $i) . '-' . sprintf("%'03d", $j);
                if (array_search($project, $r) === false) {
                    Project::create(['title' => $project]);
                    $this->info($project);
                }
            }
        }
    }
}
