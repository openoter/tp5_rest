<?php

namespace app\diudiu\model;

use think\Model;

class XfbMerchantOrder extends Model
{
    public function merchant() {
        return $this->belongsTo('XfbMerchant', 'merchantid', 'merchantid');
    }
}
