<?php


namespace App\Repositories;

use App\Models\Lga;

class LgaRepository
{
    // Fetch LGAs based on state_id
    public function getLgasByState($stateId)
    {
        return Lga::where('state_id', $stateId)->get();  // Get LGAs where state_id matches
    }
}
