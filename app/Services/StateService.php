<?php

namespace App\Services;

use App\Repositories\StateRepository;

class StateService
{
    protected StateRepository $stateRepository;

    public function __construct(StateRepository $stateRepository)
    {
        $this->stateRepository = $stateRepository;
    }

    public function getAllStates()
    {
        return $this->stateRepository->all(); // Fetching data from repository
    }
}
