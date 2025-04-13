<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollingUnit extends Model
{
    // Specifies the database table associated with this model
    protected $table = 'polling_unit';

    // Primary key
    protected $primaryKey = 'uniqueid';

    public $timestamps = false;

    // Fillable fields
    protected $fillable = [
        'polling_unit_id',
        'ward_id',
        'lga_id',
        'uniquewardid',
        'polling_unit_number',
        'polling_unit_name',
        'polling_unit_description',
        'lat',
        'long',
        'entered_by_user',
        'date_entered',
    ];


    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id', 'ward_id');
    }

    public function lga()
    {
        return $this->belongsTo(Lga::class, 'lga_id', 'lga_id');
    }


    public function agents()
    {
        return $this->hasMany(AgentName::class, 'pollingunit_uniqueid', 'polling_unit_id');
    }



    public function results()
    {
        return $this->hasMany(AnnouncedPuResults::class, 'polling_unit_uniqueid', 'uniqueid');
    }
}
