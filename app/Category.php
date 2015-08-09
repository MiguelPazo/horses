<?php namespace Horses;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function scopeTournament($query, $tournament)
    {
        return $query->where('tournament_id', $tournament);
    }

}
