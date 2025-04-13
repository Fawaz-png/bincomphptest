<?php

namespace App\Repositories;

use App\Models\State;

class StateRepository
{
    public function all()
    {
        return State::all();
    }
}
