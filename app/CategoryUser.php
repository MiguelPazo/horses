<?php namespace Horses;

use Illuminate\Database\Eloquent\Model;

class CategoryUser extends Model
{

    public $timestamps = false;
    protected $guarded = ['id'];

    public function scopeCategory($query, $id)
    {
        return $query->where('category_id', $id);
    }

}
