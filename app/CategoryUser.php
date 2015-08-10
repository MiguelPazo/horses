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

    public function scopeJury($query, $id)
    {
        return $query->where('user_id', $id);
    }

    public function scopeDiriment($query, $diriment)
    {
        return $query->where('dirimente', $diriment);
    }

}
