<?php

namespace App\Http\Controllers;

use App\Services\StateService;

class StateController extends Controller
{
    protected StateService $stateService;

    public function __construct(StateService $stateService)
    {
        $this->stateService = $stateService;
    }

    public function index()
    {
        // Use the service to get all states
        $states = $this->stateService->getAllStates();
        return response()->json($states);
    }
}
