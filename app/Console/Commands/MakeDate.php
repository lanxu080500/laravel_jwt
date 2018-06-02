<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MakeDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:makeData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '导入数据';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //$date = $this->ask('input the date');
        //$start = $this->ask('input the start');
        //$end = $this->ask('input the end');
        //$this->info("$date $start $end");
        $is_run = $this->ask('are you sure run it');
        if ($is_run) {
            DB::transaction(function (){
                $res = DB::table('line_orders_free_copy')->where('status', 1)->get();
                $res = json_encode($res);
                $res = json_decode($res, true);
                foreach ($res as $user) {
                    //$hour = sprintf("%02d", mt_rand(0, 23));
                    //$fen = sprintf("%02d", mt_rand(0, 59));
                    //$miao = sprintf("%02d", mt_rand(0, 40));
                    //$jiamiao = sprintf("%02d", $miao + 7);
                    //$created_time = substr($user['created_at'], '0', '11') . $hour . ':' . $fen . ':' . $miao;
                    //$start_time = substr($user['created_at'], '0', '11') . $hour . ':' . $fen . ':' . $jiamiao;
                    //$end_time = Carbon::parse($start_time)->addHours(12);
                    //$r = DB::table('line_orders_free_copy')
                    //    ->where('id', $user['id'])
                    //    ->update(
                    //        [
                    //            'created_at' => $created_time,
                    //            'end_at' => $end_time,
                    //            'start_at' => $start_time,
                    //        ]
                    //    );
                    $array = [1, 2, 3, 4, 5];
                    $pwd = $array[mt_rand(0, 4)] . $array[mt_rand(0, 4)] . $array[mt_rand(0, 4)] . $array[mt_rand(0, 4)] . $array[mt_rand(0, 4)] . $array[mt_rand(0, 4)] . $array[mt_rand(0, 4)];
                    $r = DB::table('line_orders_free_copy')
                        ->where('id', $user['id'])
                        ->update(['password' => $pwd]);
                    //    );
                    if ($r) {
                        $this->info("$pwd");
                    } else {
                        $this->error("error");
                    }
                }

            });
        }
    }
}
