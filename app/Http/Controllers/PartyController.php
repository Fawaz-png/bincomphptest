<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Party;

class PartyController extends Controller
{
    //
    public function index()
    {
        // Fetch all parties from the database
        $parties = new Party();

        return $parties->fetchAllParties();
    }
}
