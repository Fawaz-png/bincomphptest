<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PollingUnitController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\LgaController;
use App\Http\Controllers\WardController;
use App\Http\Controllers\PartyController;
use App\Models\PollingUnit;






Route::controller(PollingUnitController::class)->group(function () {

    // GET /api/polling-units → List all polling units with results, agents, ward, lga, state
    Route::get('/', 'index');

    // GET /api/polling-units/results/{lgaId} → Get summed vs announced results for LGA
    Route::get('polling-units/results', 'combinedResults');

    // POST /api/polling-units → Create new polling unit + its results + agents
    Route::post('/', 'store');

    Route::get('polling-units/pu', 'show');

       // GET /api/polling-units/filter?state_id=1&lga_id=2&ward_id=3 → Filter by location
    Route::get('polling-units/filter', 'filter');

});

Route::get('polling-units/parties', [PartyController::class, 'index']);


Route::get('polling-units/states', [StateController::class, 'index']);

Route::get('polling-units/lgas', [LgaController::class, 'index']);

Route::get('polling-units/wards', [WardController::class, 'index']);
