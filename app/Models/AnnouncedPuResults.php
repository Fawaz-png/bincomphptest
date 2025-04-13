<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnouncedPuResults extends Model
{
    // Specifies the database table associated with this model
    protected $table = 'announced_pu_results';

    // Primary key
    protected $primaryKey = 'result_id';

    public $timestamps = false;

    // Fillable fields
    protected $fillable = [
        'polling_unit_uniqueid',
        'party_abbreviation',
        'party_score',
        'entered_by_user',
        'date_entered',
        'user_ip_address',
    ];

    // Define the relationship with the PollingUnit model
    public function pollingUnit()
    {
        return $this->belongsTo(PollingUnit::class, 'polling_unit_uniqueid', 'uniqueid');
    }

    public function sortByDesc($attribute)
    {
        return $this->orderBy($attribute, 'desc');
    }




}
