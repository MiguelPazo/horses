<?php namespace Horses;

use Horses\Constants\ConstDb;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function scopeTournament($query, $tournament)
    {
        return $query->where('tournament_id', $tournament);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeStatusIn($query, $lstStatus)
    {
        return $query->whereIn('status', $lstStatus);
    }

    public function scopeShowable($query, $type)
    {
        $query->where('actual_stage', ConstDb::STAGE_CLASSIFY_1)
            ->orWhere('status', ConstDb::STATUS_FINAL);

        if ($type == ConstDb::TYPE_CATEGORY_SELECTION) {
            $query->orWhere('actual_stage', ConstDb::STAGE_SELECTION);
        }

        $query->where('type', $type);

        return $query;
    }

    public function juries()
    {
        return $this->belongsToMany('Horses\User', 'category_users');
    }
}
