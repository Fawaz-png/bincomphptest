<?php

namespace App\Http\Controllers;

use App\Services\LgaService;
use Illuminate\Http\Request;

class LgaController extends Controller
{
    protected LgaService $lgaService;

    // Inject LgaService into the controller
    public function __construct(LgaService $lgaService)
    {
        $this->lgaService = $lgaService;
    }

    // Get the LGAs for a given state
    public function index(Request $request)
    {
        $stateId = $request->get('state_id');

        // Fetch LGAs using LgaService
        $lgas = $this->lgaService->getLgasByState($stateId);

        return response()->json($lgas);
    }
}
