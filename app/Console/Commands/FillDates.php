<?php

namespace App\Console\Commands;

use DateInterval;
use DatePeriod;
use DateTime;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FillDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dates:fill';

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
        $period = new DatePeriod(
            new DateTime(date('Y') . '-01-01'),
            new DateInterval('P1D'),
            new DateTime(date('Y') + 1 . '-01-01')
        );

        $dates = [];
        foreach ($period as $key => $value) {
            $dates[] = $value->format('Y-m-d');
        }

        $client = new Client();

        $res = $client->request('GET','https://isdayoff.ru/api/getdata', [
            'query' => [
                'year' => date('Y'),
                'pre' => 1,
                'delimeter' => ' '
            ]
        ])->getBody();

        $res = explode(' ', $res);

        $res = array_map(function($val){
            if($val == 0)
                return 8;
            if($val == 1)
                return 0;
            if($val == 2)
                return 7;
        }, $res);

        for($i = 0; $i < count($res); $i++){
            $res[$i] = [
                'date' => $dates[$i],
                'hours' => $res[$i]
            ];
        }

        DB::table('dates')->insert($res);
    }
}
