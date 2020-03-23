<?php

namespace App;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $fillable = ['name'];

    public function getCreatedAtAttribute($attr)
    {
        return Carbon::parse($attr)->format('d.m.Y H:i');
    }

    public function getUpdatedAtAttribute($attr)
    {
        return Carbon::parse($attr)->format('d.m.Y H:i');
    }
    public function product()
    {
    	return $this->hasMany('App\Product');
    }
}
