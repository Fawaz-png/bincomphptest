<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    //specifies the database table associated with this model
    protected $table = 'ward';

    public $timestamps = false;

    protected $fillable = [
        'ward_id',
        'ward_name',
        'lga_id',
        'ward_description',
        'entered_by_user',
        'date_entered',
        'user_ip_address',
    ];

    public function pollingUnits()
    {
        return $this->hasMany(PollingUnit::class, 'ward_id', 'ward_id');
    }

    public function lga()
    {
        return $this->belongsTo(Lga::class, 'lga_id', 'lga_id');
    }
}
