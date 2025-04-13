<?php

namespace Tests\Feature;

use App\Repositories\PollingUnitRepository;
use App\Models\PollingUnit;
use App\Models\AnnouncedPuResults;
use Tests\TestCase;

class PollingUnitRepositoryTest extends TestCase
{
    protected $repo;

    public function setUp(): void
    {
        parent::setUp();
        $this->repo = new PollingUnitRepository();
    }

    /**
     * Test paginated polling units and results.
     *
     * @return void
     */
    public function testPaginatePollingUnits()
    {
        // Here, we'll assume there are multiple polling units already present in the DB
        $pollingUnits = $this->repo->paginatePollingUnits();

        // Ensure the pagination works, the result is a paginated collection.
        $this->assertNotEmpty($pollingUnits->items());
        $this->assertTrue($pollingUnits instanceof \Illuminate\Pagination\LengthAwarePaginator);
    }



    /**
     * Test filtered polling units based on state, LGA, and ward.
     *
     * @return void
     */


    /**
     * Test filtered polling units based on state, LGA, and ward.
     *
     * @return void
     */
    public function testGetFilteredPollingUnits()
    {
        // Assuming there are known records in your DB for these names
        // If needed, adjust these names to match your data
        $stateId = 25;   // Replace with actual state ID from your database
        $lgaId = 17;     // Replace with actual LGA ID
        $wardId = 181;    // Replace with actual ward ID

        // Run the method to get filtered units
        $filteredUnits = $this->repo->getFilteredPollingUnits($stateId, $lgaId, $wardId);

        // Ensure the filtering works by checking the first item
        $this->assertNotEmpty($filteredUnits->items());
        $this->assertEquals($filteredUnits->items()[0]->state_id, $stateId);
        $this->assertEquals($filteredUnits->items()[0]->lga_id, $lgaId);
        $this->assertEquals($filteredUnits->items()[0]->ward_id, $wardId);

    }


    /**
     * Test sum of polling unit results for a given LGA.
     *
     * @return void
     */
    public function testGetSumTotalResultsForLga()
    {
        // Replace with an actual LGA ID in your DB
        $lgaId = 35;

        $result = $this->repo->getSumTotalResultsForLga($lgaId);

        // Assert the results are returned for both sum_results and announced_results
        $this->assertArrayHasKey('sum_results', $result);
        $this->assertArrayHasKey('announced_results', $result);

        // Assert that sum_results and announced_results are not empty
        $this->assertNotEmpty($result['sum_results']);
        $this->assertNotEmpty($result['announced_results']);
    }
}
