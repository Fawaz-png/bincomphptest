<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    //
    protected $table = 'states';

    public function lga(){
       return $this->hasMany(Lga::class, 'state_id', 'state_id');
    }
}
