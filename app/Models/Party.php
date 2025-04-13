<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
    //
    protected $table = 'party';

    protected $fillable = [
        'partyid',
        'partyname',
    ];

    public function pollingUnits()
    {
        return $this->hasMany(AnnouncedPuResults::class, 'party_id', 'party_id');
    }
    public function fetchAllParties()
    {
        return $this->all();
    }

}
