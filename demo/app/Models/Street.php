<?php

namespace Lego\Demo\Models;

use Illuminate\Database\Eloquent\Model;

class Street extends Model
{
    public function suites()
    {
        return $this->hasMany(Suite::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}

