<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class JiaData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:jia';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '导入假数据';

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
        //免费订单
        $res = DB::table('line_orders_free_copy')->orderBy('created_at', 'asc')->offset(40000)->limit(10000)->get();
        $res = json_encode($res);
        $res = json_decode($res, true);
        foreach ($res as $user) {
            $id = $user['id'];
            unset($user['id']);
            $r = DB::table('line_orders_free_copy1')->insert($user);
            if ($r) {
                if ($r) {
                    $this->info($id);
                } else {
                    $this->error("error");
                }
            }
        }
        //不免费订单
        //$res = DB::table('line_orders')->where('shop_id', '>', 1)->offset(40000)->limit(400)->get();
        //$res = json_encode($res);
        //$res = json_decode($res, true);
        //foreach ($res as $user) {
        //    $id = $user['id'];
        //    unset($user['id']);
        //    $r = DB::table('line_orders_free')->insert($user);
        //    if ($r) {
        //        if ($r) {
        //            $this->info($id);
        //        } else {
        //            $this->info("error");
        //        }
        //    }
        //}
        //用户
        //$res = DB::table('customers_copy')->orderBy('created_at', 'asc')->offset(90000)->limit(31000)->get();
        //$res = json_encode($res);
        //$res = json_decode($res, true);
        //foreach ($res as $user) {
        //    $id = $user['id'];
        //    unset($user['id']);
        //    $r = DB::table('customers_copy_copy')->insert($user);
        //    if ($r) {
        //        if ($r) {
        //            $this->info($id);
        //        } else {
        //            $this->error("error");
        //        }
        //    }
        //}
    }
}
