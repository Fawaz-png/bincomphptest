<?php

namespace App\Repositories;

use App\DTOs\PollingResultDTO;
use App\Models\AnnouncedPuResults;
use App\Models\PollingUnit;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\Lga;
use App\Models\Ward;



class PollingUnitRepository
{
    /**
     * Paginate polling units with results, agent, and their location hierarchy (lga → state).
     */
    public function paginatePollingUnits($perPage = 10)
    {
        // Eager load related data to avoid N+1 query issues
        return PollingUnit::with([
            'results',  // Ensure results are fetched for each polling unit
            'lga.state',  // Load state for the LGA
            'ward',  // Load the ward related to the polling unit
            'agents'  // Load agents related to the polling unit
        ])
            ->paginate($perPage)  // Paginate the results based on the perPage value
            ->through(function ($unit) {
                return PollingResultDTO::fromModelForListing($unit)->toArray();
            });
    }

    /**
     * Fetch polling units based on the provided ward ID.
     *
     * @param  int $wardId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByWard($wardId)
    {
        return PollingUnit::where('ward_id', $wardId)->get(); // Get polling units where ward_id matches
    }


    /**
     * Fetch polling unit results based on provided filters (state, LGA, ward).
     *
     * @param  int|null $stateId
     * @param  int|null $lgaId
     * @param  int|null $wardId
     * @param  int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getFilteredPollingUnits($stateId = null, $lgaId = null, $wardId = null, $pollingUnitId = null, $perPage = 10)
    {
        // Start building the query
        $query = AnnouncedPuResults::with([
            'pollingUnit.ward.lga.state', // Nested relationships for polling unit, ward, lga, and state
        ]);

        // Apply filters if IDs are provided
        if ($stateId) {
            $query->whereHas('pollingUnit.ward.lga.state', function (Builder $query) use ($stateId) {
                $query->where('state_id', $stateId);
            });
        }

        if ($lgaId) {
            $query->whereHas('pollingUnit.ward.lga', function (Builder $query) use ($lgaId) {
                $query->where('lga_id', $lgaId);
            });
        }

        if ($wardId) {
            $query->whereHas('pollingUnit.ward', function (Builder $query) use ($wardId) {
                $query->where('ward_id', $wardId);  // Assuming 'ward_id' is the column for ward identification
            });
        }

        if ($pollingUnitId) {
            // Ensure the results have a matching polling_unit_uniqueid in the announced_pu_results table
            $query->whereHas('pollingUnit', function (Builder $query) use ($pollingUnitId) {
                $query->where('uniqueid', $pollingUnitId); // Use uniqueid for polling unit in the filter
            });
        }

        // Paginate results
        return $query->paginate($perPage)
            ->through(function ($result) {
                // Assuming you are transforming the results using a DTO for details
                return ($result);
            });
    }


    /**
     * Fetch the sum of all polling unit results under an LGA and the announced results.
     *
     * @param  string $lgaName
     * @return array
     */
    public function getSumTotalResultsForLga($lgaId)
    {
        // Fetch the polling units and their results
        $pollingUnits = PollingUnit::where('lga_id', $lgaId)
            ->with(['results', 'lga.state', 'ward', 'agents'])
            ->get();

        // Sum the results for each party from the polling unit results
        $sumResults = $pollingUnits->flatMap(function ($unit) {
            return $unit->results;
        })
            ->groupBy('party_abbreviation')
            ->map(function ($partyResults) {
                return $partyResults->sum('party_score');
            });

        // Fetch the officially announced results for this LGA
        $announcedResults = AnnouncedPuResults::whereHas('pollingUnit', function ($query) use ($lgaId) {
            $query->where('lga_id', $lgaId);
        })
            ->get()
            ->groupBy('party_abbreviation')
            ->map(function ($partyResults) {
                return $partyResults->sum('party_score');
            });

        // Return the data as an array
        return [
            'sum_results' => $sumResults,
            'announced_results' => $announcedResults,
        ];
    }


    /**
     * Store a new polling unit result entry.
     *
     * @param  array  $data
     * @return PollingUnit
     */

    public function createOrGetLga(?string $lgaName, ?int $lgaId, int $stateId, string $enteredByUser): int
    {
        if ($lgaId) return $lgaId;

        $lga = Lga::firstOrCreate(
            ['lga_name' => $lgaName],
            [
                'lga_id' => Lga::max('lga_id') + 1,
                'state_id' => $stateId,
                'entered_by_user' => $enteredByUser,
                'date_entered' => now(),
                'user_ip_address' => request()->ip(),
            ]
        );

        return $lga->lga_id;
    }

    public function createOrGetWard($wardName, $wardId, $lgaId, $enteredBy)
    {
        // Check if the Ward exists, if not create it
        $ward = Ward::firstOrCreate(
            ['ward_id' => $wardId],
            [
                'lga_id' => $lgaId,
                'ward_name' => $wardName,
                'entered_by_user' => $enteredBy,
                'ward_description' => null,  // or some default value
                'date_entered' => now(),
                'user_ip_address' => request()->ip(),  // Assuming you're storing the IP address
            ]
        );

        // Return the ward_id (this is the primary key for the Ward table)
        return $ward->ward_id;  // Return the ward_id, not the whole model
    }

    public function createPollingUnit(array $data): PollingUnit
    {
        return PollingUnit::create([
            'polling_unit_id' => $data['polling_unit_id'],
            'polling_unit_number' => $data['polling_unit_number'],
            'polling_unit_name' => $data['polling_unit_name'],
            'polling_unit_description' => $data['polling_unit_description'],
            'lat' => $data['lat'],
            'long' => $data['long'],
            'entered_by_user' => $data['entered_by_user'],
            'date_entered' => now(),
            'user_ip_address' => request()->ip(),
            'ward_id' => $data['ward_id'],
            'lga_id' => $data['lga_id'],
        ]);
    }

    public function storePartyResults(int $pollingUnitId, array $partyScores, string $enteredByUser): void
    {
        foreach ($partyScores as $score) {
            AnnouncedPuResults::create([
                'polling_unit_uniqueid' => $pollingUnitId,
                'party_abbreviation' => $score['party_id'],
                'party_score' => $score['score'],
                'entered_by_user' => $enteredByUser,
                'date_entered' => now(),
                'user_ip_address' => request()->ip(),
            ]);
        }
    }
}
