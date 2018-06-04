<?php

namespace app\diudiu\model;

use think\Model;

class XfbMerchant extends Model
{

    public function merchants(){
        return $this->belongsTo('XfbMerchants', 'merchantid', 'merchantid');
    }

    public function merchantOrder() {
        return $this->hasMany('XfbMerchantOrder', 'merchantid', 'merchantid');
    }
}
