<?php namespace Lego\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class ExampleModel extends Model
{
    public function test_belongs_to()
    {
        return $this->belongsTo(BelongsToExample::class);
    }
}
