<?php

// app/Services/LgaService.php
namespace App\Services;

use App\Repositories\LgaRepository;

class LgaService
{
    protected $lgaRepository;

    public function __construct(LgaRepository $lgaRepository)
    {
        $this->lgaRepository = $lgaRepository;
    }

    // Get LGAs by state_id
    public function getLgasByState($stateId)
    {
        return $this->lgaRepository->getLgasByState($stateId);  // Call repository to fetch LGAs
    }
}
