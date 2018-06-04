<?php
namespace app\diudiu\controller;

use app\diudiu\model\XfbMerchant;
use app\diudiu\model\XfbMerchants;

class Index
{
    public function index()
    {
       /* $d = (new XfbMerchants())->with(["merchant"=>function($query){
            $query->withCount(['merchantOrder'=>function($query){
                $time = 1521907200;
                $end = 1521993599;
                $query->order('rec_time desc')->where('rec_time', 'between', "{$time}, {$end}");
            }]);

        }])->find(25)->toArray();*/




        $s = (new XfbMerchants())->with('orders')->find(23)->toArray();
        return json($s);
    }
}
