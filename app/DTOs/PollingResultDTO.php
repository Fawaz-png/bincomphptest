<?php

namespace App\DTOs;

use App\Repositories\PollingUnitRepository;
use Illuminate\Support\Collection;

class PollingResultDTO
{
    public $pollingUnitName;
    public $pollingUnitId;  // This should be the correct name in the model
    public $uniqueId;
    public $pollingUnitNumber;
    public $pollingUnitDescription;
    public $lat;
    public $long;
    public $enteredByUser;
    public $dateEntered;
    public $stateName;
    public $lgaName;
    public $wardName;
    public $agentNames;
    public $results;
    public $winningResult;
    public $stateId;
    public $lgaId;
    public $wardId; // Ensure this matches Ward's uniqueid

    public function __construct(
        $pollingUnitName,
        $pollingUnitId,  // This corresponds to the correct name in the model
        $uniqueId,
        $pollingUnitNumber,
        $pollingUnitDescription,
        $lat,
        $long,
        $enteredByUser,
        $dateEntered,
        $stateName,
        $lgaName,
        $wardName,
        $agentNames,
        $results,
        $winningResult,
        $stateId,
        $lgaId,
        $wardId
    ) {
        $this->pollingUnitName = $pollingUnitName;
        $this->pollingUnitId = $pollingUnitId; // Ensure this is correctly passed
        $this->uniqueId = $uniqueId;
        $this->pollingUnitNumber = $pollingUnitNumber;
        $this->pollingUnitDescription = $pollingUnitDescription;
        $this->lat = $lat;
        $this->long = $long;
        $this->enteredByUser = $enteredByUser;
        $this->dateEntered = $dateEntered;
        $this->stateName = $stateName;
        $this->lgaName = $lgaName;
        $this->wardName = $wardName;
        $this->agentNames = $agentNames;
        $this->results = $results;
        $this->winningResult = $winningResult;
        $this->stateId = $stateId;
        $this->lgaId = $lgaId;
        $this->wardId = $wardId; // Ensure this matches Ward's uniqueid
    }


    // Format results for listing and pagination
    public static function fromModelForListing($pollingUnit): self
    {
        $results = $pollingUnit->results?->map(function ($result) {
            return [
                'party' => $result->party_abbreviation,
                'score' => $result->party_score,
            ];
        }) ?? collect();

        $agentNames = $pollingUnit->agents?->map(
            fn($agent) => "{$agent->firstname} {$agent->lastname}"
        )->join(', ') ?? '';

        $winningResult = $results->isNotEmpty()
            ? $results->sortByDesc('score')->first()
            : (object)['party' => null, 'score' => 0];


        // Safe access to wardId and pollingUnitId
        $wardId = $pollingUnit->ward ? $pollingUnit->ward->ward_id : '';
        $pollingUnitId = $pollingUnit->polling_unit_id ?? '';  // Ensure polling_unit_id is available

        return new self(
            $pollingUnit->polling_unit_name ?? '',
            $pollingUnitId,  // Pass the correct pollingUnitId
            $pollingUnit->unique_id ?? '', // Polling Unit unique_id
            $pollingUnit->polling_unit_number ?? '',
            $pollingUnit->polling_unit_description ?? '',
            $pollingUnit->lat ?? '',
            $pollingUnit->long ?? '',
            $pollingUnit->entered_by_user ?? '',
            $pollingUnit->date_entered ?? '',
            $pollingUnit->lga->state->state_name ?? '',
            $pollingUnit->lga->lga_name ?? '',
            $pollingUnit->ward->ward_name ?? '',
            $agentNames,
            $results,
            $winningResult,
            $pollingUnit->lga->state->state_id ?? '',
            $pollingUnit->lga->lga_id ?? '',
            $wardId // Use the safe access for ward's uniqueid
        );
    }


    public function toArray(): array
    {
        return [
            'polling_unit_name' => $this->pollingUnitName,
            'polling_unit_id' => $this->pollingUnitId,
            'unique_id' => $this->uniqueId,
            'polling_unit_number' => $this->pollingUnitNumber,
            'polling_unit_description' => $this->pollingUnitDescription,
            'lat' => $this->lat,
            'long' => $this->long,
            'entered_by_user' => $this->enteredByUser,
            'date_entered' => $this->dateEntered,
            'state_name' => $this->stateName,
            'lga_name' => $this->lgaName,
            'ward_name' => $this->wardName,
            'agent_names' => $this->agentNames,
            'results' => $this->results,
            'winning_result' => $this->winningResult,
            'state_id' => $this->stateId,
            'lga_id' => $this->lgaId,
            'ward_id' => $this->wardId,
        ];
    }


    // Format individual polling unit (for details)
    public static function fromModelForDetails($pollingUnit): self
    {
        return self::fromModelForListing($pollingUnit);
    }


    // Format sum results (total results for a given LGA)
    public static function fromCollectionForSummedResults($pollingUnits, $sumResults = null): self
    {
        // Format the results using the summed scores passed in
        $results = collect($sumResults)->map(function ($score, $party) {
            return [
                'party' => $party,
                'total_score' => $score,
            ];
        })->values();

        // Determine the party with the highest score
        $winningResult = $results->sortByDesc('total_score')->first();

        // Use the first polling unit to get location details
        $firstUnit = $pollingUnits->first();

        return new self(
            $firstUnit->polling_unit_name,
            $firstUnit->polling_unit_id,
            $firstUnit->unique_id,
            $firstUnit->polling_unit_number,
            $firstUnit->polling_unit_description,
            $firstUnit->lat,
            $firstUnit->long,
            $firstUnit->entered_by_user,
            $firstUnit->date_entered,
            $firstUnit->lga->state->state_name,
            $firstUnit->lga->lga_name,
            $firstUnit->ward->ward_name,
            $firstUnit->agents?->map(fn($a) => "{$a->firstname} {$a->lastname}")->join(', ') ?? '',
            $results,
            $winningResult,
            $firstUnit->lga->state->state_id ?? '',
            $firstUnit->lga->lga_id ?? '',
            $firstUnit->ward->uniqueid ?? ''
        );
    }

    // Method to format polling unit results for storage (optional)
    public static function fromInputData($inputData): self
    {
        return new self(
            $inputData['polling_unit_name'],
            $inputData['polling_unit_id'],
            $inputData['unique_id'],
            $inputData['polling_unit_number'],
            $inputData['polling_unit_description'],
            $inputData['lat'],
            $inputData['long'],
            $inputData['entered_by_user'],
            $inputData['date_entered'],
            $inputData['state_name'],
            $inputData['lga_name'],
            $inputData['ward_name'],
            $inputData['agent_names'],
            $inputData['results'],
            $inputData['winning_result'],
            $inputData['state_id'], // Add state_id
            $inputData['lga_id'],    // Add lga_id
            $inputData['ward_id']   // Add ward_id
        );
    }
}
