<?php

namespace App\Repositories;

use App\Models\Ward;


class WardRepository
{
    /**
     * Fetch wards based on lga_id
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
   public function getWardsByLga($lgaId)
    {
        return Ward::where('lga_id', $lgaId)->get();  // Get wards where lga_id matches
    }

}