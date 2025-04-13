<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lga extends Model
{
    // Specifies the database table associated with this model
    protected $table = 'lga';

    public $timestamps = false;
    
    protected $fillable = [
        'lga_id',
        'state_id',
        'lga_name',
        'lga_description',
        'entered_by_user',
        'date_entered',
        'user_ip_address',
    ];

    //
    public function state()
    {
       return $this->belongsTo(State::class, 'state_id', 'state_id');
    }

    public function wards()
    {
        return $this->hasMany(Ward::class, 'lga_id', 'lga_id');
    }
}
