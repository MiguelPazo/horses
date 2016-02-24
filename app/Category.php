<?php namespace Horses;

use Horses\Constants\ConstDb;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = strtoupper($value);
    }

    public function scopeTournament($query, $tournament)
    {
        return $query->where('tournament_id', $tournament);
    }

    public function scopeStatus($query, $status, $or = false, $diff = false)
    {
        $operator = ($diff) ? '<>' : '=';
        $query = ($or) ? $query->orWhere('status', $operator, $status) : $query->where('status', $operator, $status);
        return $query;
    }

    public function scopeStatusDiff($query, $status)
    {
        return $query->where('status', '<>', $status);
    }

    public function scopeStatusIn($query, $lstStatus)
    {
        return $query->whereIn('status', $lstStatus);
    }

    public function scopeIdsIn($query, $ids)
    {
        return $query->whereIn('id', $ids);
    }

    public function scopeShowable($query, $type)
    {
        if ($type == ConstDb::TYPE_CATEGORY_SELECTION) {
            return $query->where('type', ConstDb::TYPE_CATEGORY_SELECTION)
                ->where('actual_stage', '<>', '')
                ->where('actual_stage', '<>', ConstDb::STAGE_ASSISTANCE);
        } else {
            return $query->where('type', ConstDb::TYPE_CATEGORY_WSELECTION)
                ->where('actual_stage', '<>', '')
                ->where('actual_stage', '<>', ConstDb::STAGE_ASSISTANCE)
                ->where('actual_stage', '<>', ConstDb::STAGE_SELECTION);
        }
    }

    public function juries()
    {
        return $this->belongsToMany('Horses\User', 'category_users');
    }
}
