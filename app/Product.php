<?php

namespace App;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name','price','category_id'];

    public function getCreatedAtAttribute($attr)
    {
        return Carbon::parse($attr)->format('d.m.Y H:i');
    }

    public function getUpdatedAtAttribute($attr)
    {
        return Carbon::parse($attr)->format('d.m.Y H:i');
    }
    public function category()
    {
    	return $this->belongsTo('App\Category');
    }
}
