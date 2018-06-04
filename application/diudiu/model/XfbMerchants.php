<?php

namespace app\diudiu\model;

use think\Model;

class XfbMerchants extends Model
{
    public function merchant() {
        return $this->hasMany('XfbMerchant', 'invitecode', 'invitecode');
    }

    public function orders() {
        return $this->hasManyThrough('XfbMerchantOrder', 'XfbMerchant', 'merchantid', 'merchantid', 'merchantid', 'invitecode');
    }
}
