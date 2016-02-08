<?php namespace Horses;

use Illuminate\Database\Eloquent\Model;

class Catalog extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function animals()
    {
        return $this->belongsTo('Horses\Animal');
    }

    public function scopeTournament($query, $id)
    {
        return $query->where('tournament_id', $id);
    }

    public function scope()
    {

    }

}
