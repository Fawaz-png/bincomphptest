<?php

namespace App\Services;

use App\Repositories\PollingUnitRepository;
use Illuminate\Support\Facades\DB;
use App\DTOs\AnnouncedResultDTO;

class PollingUnitService
{
    public function __construct(protected PollingUnitRepository $repository) {}

    /**
     * Get paginated polling units for listing
     */
    public function listPaginatedPollingUnits(int $perPage = 10)
    {
        return $this->repository->paginatePollingUnits($perPage);
    }


    /**
     * Get polling unit by Ward ID
     */
    public function getPollingUnitByWard($wardId)
    {
        return $this->repository->findByWard($wardId);
    }

    /**
     * Get filtered polling units based on state, lga, and ward
     */
    public function getFilteredPollingUnits(?int $stateId, ?int $lgaId, ?int $wardId, ?int $pollingUnitId, int $perPage = 10)
    {
        return $this->repository->getFilteredPollingUnits($stateId, $lgaId, $wardId, $pollingUnitId, $perPage);
    }


    /**
     * Get both summed results and officially announced results for a given LGA.
     */
    public function getCombinedResultsByLga(int $lgaId): array
    {
        // Fetch the results from the repository
        $results = $this->repository->getSumTotalResultsForLga($lgaId);

        // Return the raw summed results and announced results directly
        return [
            'summed_results' => $results['sum_results'],  // Summed results for each party
            'announced_results' => $results['announced_results'],  // Announced results for each party
        ];
    }


    /**
     * Store a new polling unit with its results and agents.
     */
    public function handleCreatePollingUnit(array $payload)
    {
        return DB::transaction(function () use ($payload) {
            $enteredBy = $payload['entered_by_user'] ?? 'anonymous';

            // Create or fetch the LGA and Ward IDs
            $lgaId = $this->repository->createOrGetLga($payload['lga_name'], $payload['lga_id'], $payload['state_id'], $enteredBy);
            $wardId = $this->repository->createOrGetWard($payload['ward_name'], $payload['ward_id'], $lgaId, $enteredBy);

            // Create Polling Unit and fetch its ID
            $pollingUnit = $this->repository->createPollingUnit([
                ...$payload,
                'lga_id' => $lgaId,
                'ward_id' => $wardId,
                'entered_by_user' => $enteredBy,
            ]);

            // Store party results if they exist
            if (!empty($payload['party_scores'])) {
                $this->repository->storePartyResults($pollingUnit->polling_unit_id, $payload['party_scores'], $enteredBy);
            }

            return $pollingUnit;
        });
    }
}
