<?php

namespace Tests\Feature;


use App\Repositories\PollingUnitRepository;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Lga;
use App\Models\Ward;
use App\Models\PollingUnit;
use App\Models\AnnouncedPuResults;

class CreatePollingUnitTest extends TestCase
{

    protected PollingUnitRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new PollingUnitRepository(); // Or use mock()
    }

    /** @test */
    public function it_creates_polling_unit_and_party_results()
    {
        // Assume we have a service class
        $repo = new PollingUnitRepository();

        // Step 1: Create LGA (since we pass null for ID)
        $lgaId = $repo->createOrGetLga('Yaba', null, 25, 'fawaz');

        $this->assertDatabaseHas('lga', [
            'lga_id' => $lgaId,
            'lga_name' => 'Yaba',
            'state_id' => 25,
        ]);

        // Step 2: Create Ward (ID is not found, so new one will be created)
        $wardId = $repo->createOrGetWard('Sabo Ward', 301, $lgaId, 'fawaz');

        $this->assertDatabaseHas('ward', [
            'ward_id' => $wardId,
            'ward_name' => 'Sabo Ward',
            'lga_id' => $lgaId,
        ]);

        // Step 3: Create Polling Unit
        $pollingUnit = $repo->createPollingUnit([
            'polling_unit_id' => 1234,
            'polling_unit_number' => 'PU-1234',
            'polling_unit_name' => 'Sabo Primary School',
            'polling_unit_description' => 'Beside market',
            'lat' => '6.5244',
            'long' => '3.3792',
            'entered_by_user' => 'fawaz',
            'ward_id' => $wardId,
            'lga_id' => $lgaId,
        ]);

        $this->assertDatabaseHas('polling_unit', [
            'polling_unit_id' => 1234,
            'polling_unit_name' => 'Sabo Primary School',
            'ward_id' => $wardId,
            'lga_id' => $lgaId,
        ]);

        // Step 4: Store party results
        $repo->storePartyResults($pollingUnit->polling_unit_id, [
            ['party_id' => 'PDP', 'score' => 80],
            ['party_id' => 'APC', 'score' => 120],
        ], 'fawaz');

        $this->assertDatabaseHas('announced_pu_results', [
            'polling_unit_uniqueid' => 1234,
            'party_abbreviation' => 'PDP',
            'party_score' => 80,
        ]);

        $this->assertDatabaseHas('announced_pu_results', [
            'polling_unit_uniqueid' => 1234,
            'party_abbreviation' => 'APC',
            'party_score' => 120,
        ]);
    }
}
