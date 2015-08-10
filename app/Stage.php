<?php namespace Horses;

use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{

    public $timestamps = false;
    protected $guarded = ['id'];

    public function scopeStage($query, $stage)
    {
        return $query->where('stage', $stage);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeCompetitor($query, $idCompetitor)
    {
        return $query->where('competitor_id', $idCompetitor);
    }

    public function scopeJury($query, $idJury)
    {
        return $query->where('user_id', $idJury);
    }

    public function scopeJuryIn($query, $lstIdJury)
    {
        return $query->whereIn('user_id', $lstIdJury);
    }

}
