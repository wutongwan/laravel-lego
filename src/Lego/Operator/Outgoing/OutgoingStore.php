<?php namespace Lego\Operator\Outgoing;

use Illuminate\Support\Collection;
use Lego\Foundation\Exceptions\NotSupportedException;
use Lego\Operator\Store;

class OutgoingStore extends Store
{
    use OutgoingParser;

    public function toArray()
    {
        throw new NotSupportedException();
    }

    public function get($attribute, $default = null)
    {
        throw new NotSupportedException();
    }

    public function set($attribute, $value)
    {
        throw new NotSupportedException();
    }

    public function getAssociated($attribute)
    {
        throw new NotSupportedException();
    }

    public function associate($attribute, $id)
    {
        throw new NotSupportedException();
    }

    public function dissociate($attribute)
    {
        throw new NotSupportedException();
    }

    public function getAttached($attribute): Collection
    {
        throw new NotSupportedException();
    }

    public function attach($attribute, array $ids, array $attributes = [])
    {
        throw new NotSupportedException();
    }

    public function detach($attribute, array $ids)
    {
        throw new NotSupportedException();
    }

    public function save($options = [])
    {
    }
}
