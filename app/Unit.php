<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'unit'
    ];

    public function products() {
        return $this->hasMany(Product::class);
    }
}
