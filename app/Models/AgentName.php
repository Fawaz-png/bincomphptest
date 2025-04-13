<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentName extends Model
{
    //specifies the database table associated with this model
    protected $table = 'agentname';

    public function pollingUnit()
    {
        return $this->belongsTo(PollingUnit::class, 'pollingunit_uniqueid', 'polling_unit_id');
    }
}
