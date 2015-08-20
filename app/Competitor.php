<?php namespace Horses;

use Illuminate\Database\Eloquent\Model;

class Competitor extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function scopeCategory($query, $id)
    {
        return $query->where('category_id', $id);
    }

    public function scopePosition($query, $position)
    {
        return $query->where('position', $position);
    }

    public function scopeIdIn($query, $ids)
    {
        return $query->whereIn('id', $ids);
    }

}
