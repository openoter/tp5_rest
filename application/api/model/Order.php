<?php

namespace app\api\model;


class Order extends BaseModel{
    protected $hidden = ['user_id', 'delete_time', 'update_time'];
    protected $autoWriteTimestamp = true;
}