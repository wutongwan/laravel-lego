<?php

namespace Lego\Demo\Models;

use Illuminate\Database\Eloquent\Model;

class Suite extends Model
{
    public static function listType()
    {
        return ['平层', '复式', '集中公寓'];
    }

    public static function listStatus()
    {
        return ['待装修', '硬装中', '现房', '已出租'];
    }

    public function street()
    {
        return $this->belongsTo(Street::class);
    }
}
