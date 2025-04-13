<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PollingUnitService;
use App\Repositories\PollingUnitRepository;
use Illuminate\Http\JsonResponse;

class PollingUnitController extends Controller
{
    protected PollingUnitService $pollingUnitService;

    public function __construct(PollingUnitService $pollingUnitService)
    {
        $this->pollingUnitService = $pollingUnitService;
    }

    /**
     * List all polling units with results and related data.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        // Fetch the data from the service (this remains the same)
        $pollingUnits = $this->pollingUnitService->listPaginatedPollingUnits($perPage);

        // Return a view with the data (passing data to a Blade view)
        return view('polling_units', compact('pollingUnits'));
    }


    public function show(Request $request)
    {
        $wardId = $request->get('ward_id');
        // Fetch polling unit by Ward
        $pollingUnit = $this->pollingUnitService->getPollingUnitByWard($wardId);

        return response()->json($pollingUnit);
    }

    /**
     * Filter polling units by state, LGA, and ward.
     */
    public function filter(Request $request)
    {

        $stateId = (int) $request->get('filter_state_id');
        $lgaId = (int) $request->get('filter_lga_id');
        $wardId = (int) $request->get('filter_ward_id');
        $pollingUnitId =  $request->get('filter_polling_unit_id');
        $perPage = (int) $request->get('per_page', 10);

        return response()->json(
            $this->pollingUnitService->getFilteredPollingUnits($stateId, $lgaId, $wardId, $pollingUnitId, $perPage)
        );
    }




    /**
     * Get combined polling results for a given LGA (summed + officially announced).
     */
    public function combinedResults(Request $request)
    {
        $lgaId = $request->query('lgaId', 35); // Defaults to 35 if none provided

        $combinedResults = $this->pollingUnitService->getCombinedResultsByLga($lgaId);

        return response()->json($combinedResults);  // Return as a JSON response

    }


    /**
     * Store new polling unit results.
     */
    public function createPollingUnitResult(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            'state_id' => 'required|integer',
            'lga_id' => 'nullable|integer',
            'lga_name' => 'nullable|string|max:255',
            'ward_id' => 'nullable|integer',
            'ward_name' => 'nullable|string|max:255',
            'polling_unit_id' => 'required|integer',
            'polling_unit_number' => 'nullable|string|max:255',
            'polling_unit_name' => 'required|string|max:255',
            'polling_unit_description' => 'nullable|string',
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric',
            'entered_by_user' => 'required|string|max:255',
            'party_scores' => 'nullable|array',
            'party_scores.*.party_name' => 'required|string|max:255',
            'party_scores.*.score' => 'required|integer',
        ]);

        try {
            // Call the service layer to handle the polling unit creation logic
            $pollingUnit = $this->pollingUnitService->handleCreatePollingUnit($validated);

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Polling unit result created successfully!',
                'data' => $pollingUnit
            ], 201);
        } catch (\Exception $e) {
            // Handle exceptions and return error message
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while creating the polling unit result.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
