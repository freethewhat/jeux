<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    public $timestamps = false;

    public function game() {
      return $this->hasOne('App\Game');
    }

    public function owner() {
      return $this->hasMany('App\User');
    }
}
