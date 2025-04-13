<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Polling Unit Results</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-100 min-h-screen text-gray-800">
    <div class="max-w-6xl mx-auto py-8 px-4 space-y-12">
        <h1 class="text-4xl font-extrabold text-center text-blue-800">Polling Unit Results Dashboard</h1>
        <p class="text-center text-gray-600 text-lg">Search and view results for polling units across states, LGAs, and
            wards.</p>

        <!-- Filter Form -->
        <form id="filterPollingUnitsForm" class="bg-white shadow-md rounded-lg p-6 space-y-6">
            <h2 class="text-xl font-semibold text-gray-700">Filter Polling Units</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <select id="filterState" name="filter_state_id" class="border border-gray-300 rounded-lg p-3" required>
                    <option value="">Select State</option>
                </select>
                <select id="filterLga" name="filter_lga_id" class="border border-gray-300 rounded-lg p-3" disabled
                    required>
                    <option value="">Select LGA</option>
                </select>
                <select id="filterWard" name="filter_ward_id" class="border border-gray-300 rounded-lg p-3" disabled
                    required>
                    <option value="">Select Ward</option>
                </select>
                <select id="filterPollingUnit" name="filter_polling_unit_id"
                    class="border border-gray-300 rounded-lg p-3" disabled>
                    <option value="">Select Polling Unit</option>
                </select>
            </div>
            <div class="text-right">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Search
                </button>
            </div>
        </form>

        <!-- Filtered Results Table -->
        <div id="filteredResultsTable" class="bg-white shadow-md rounded-lg p-6 hidden">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Filtered Polling Units</h2>
            <table class="w-full border-collapse border border-gray-300 text-sm">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="border p-3">Party</th>
                        <th class="border p-3">Score</th>
                        <th class="border p-3">PU Name</th>
                        <th class="border p-3">Ward</th>
                        <th class="border p-3">LGA</th>
                        <th class="border p-3">State</th>
                        <th class="border p-3">Entered By</th>
                        <th class="border p-3">Date Entered</th>
                    </tr>
                </thead>
                <tbody id="filteredTableBody">
                </tbody>
            </table>
        </div>

        <!-- All Polling Units Table -->
        <h2 class="text-xl font-semibold text-gray-700">All Polling Units</h2>
        <table class="w-full border-collapse border border-gray-300 text-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border p-3">PU ID</th>
                    <th class="border p-3">PU Name</th>
                    <th class="border p-3">State</th>
                    <th class="border p-3">LGA</th>
                    <th class="border p-3">Ward</th>
                    <th class="border p-3">Agent(s)</th>
                    <th class="border p-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pollingUnits as $unit)
                    <tr class="hover:bg-gray-100">
                        <td class="border p-3">{{ $unit['polling_unit_id'] }}</td>
                        <td class="border p-3">{{ $unit['polling_unit_name'] }}</td>
                        <td class="border p-3">{{ $unit['state_name'] }}</td>
                        <td class="border p-3">{{ $unit['lga_name'] }}</td>
                        <td class="border p-3">{{ $unit['ward_name'] }}</td>
                        <td class="border p-3">{{ $unit['agent_names'] }}</td>
                        <td class="border p-3">
                            <button onclick='openModal(@json($unit))'
                                class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                                View Results
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-4 text-center text-gray-500">No polling units found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $pollingUnits->links() }}
        </div>

        <!-- LGA Results -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">View Summed Results by LGA</h2>
            <form id="lgaResultsForm" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="lgaState" class="block text-sm font-medium text-gray-700 mb-1">Select State</label>
                    <select id="lgaState" name="lga_state" class="w-full border border-gray-300 rounded-lg p-3"
                        required>
                        <option value="">-- Choose State --</option>
                    </select>
                </div>
                <div>
                    <label for="lgaLga" class="block text-sm font-medium text-gray-700 mb-1">Select LGA</label>
                    <select id="lgaLga" name="lga_lga" class="w-full border border-gray-300 rounded-lg p-3" required>
                        <option value="">-- Choose LGA --</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit"
                        class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                        View Result
                    </button>
                </div>
            </form>
        </div>

        <!-- LGA Results Table -->
        <div id="lgaResultsContainer" class="bg-green-50 shadow-md rounded-lg p-6 hidden">
            <h2 class="text-lg font-bold text-green-700 mb-4" id="lgaResultsHeader">Summed Results for LGA:</h2>
            <table class="w-full border-collapse border border-gray-300 text-sm">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="border p-3">Party</th>
                        <th class="border p-3">Summed Total Score</th>
                        <th class="border p-3">Announced Total Score</th>
                    </tr>
                </thead>
                <tbody id="lgaResultsTableBody">
                </tbody>
            </table>
        </div>

        <!-- Add New Polling Unit Result -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Add New Polling Unit Result</h2>
            <form id="addPollingUnitForm" class="space-y-6">
                @csrf
                <div class="grid md:grid-cols-2 gap-4">
                    <!-- State Select -->
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State</label>
                        <select id="state" name="state_id" class="border border-gray-300 rounded-lg p-3 w-full"
                            required>
                            <option value="">Select State</option>
                        </select>
                    </div>

                    <!-- LGA Select -->
                    <div>
                        <label for="lga" class="block text-sm font-medium text-gray-700 mb-1">LGA</label>
                        <div class="flex gap-2">
                            <select id="lga" name="lga_id" class="border border-gray-300 rounded-lg p-3 w-full"
                                required>
                                <option value="">Select LGA</option>
                            </select>
                            <input type="text" id="lga_name" name="lga_name" class="hidden" />
                            <button type="button" id="addLgaButton"
                                class="bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700">
                                Add LGA
                            </button>
                        </div>
                    </div>

                    <!-- Ward Select -->
                    <div>
                        <label for="ward" class="block text-sm font-medium text-gray-700 mb-1">Ward</label>
                        <div class="flex gap-2">
                            <select id="ward" name="ward_id" class="border border-gray-300 rounded-lg p-3 w-full"
                                required>
                                <option value="">Select Ward</option>
                            </select>
                            <input type="text" id="ward_name" name="ward_name" class="hidden" />
                            <button type="button" id="addWardButton"
                                class="bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700">
                                Add Ward
                            </button>
                        </div>
                    </div>

                    <!-- Polling Unit ID -->
                    <input type="number" name="polling_unit_id" placeholder="Polling Unit ID"
                        class="border border-gray-300 rounded-lg p-3 w-full" required />

                    <!-- Polling Unit Number -->
                    <input type="text" name="polling_unit_number" placeholder="Polling Unit Number"
                        class="border border-gray-300 rounded-lg p-3 w-full" />

                    <!-- Polling Unit Name -->
                    <input type="text" name="polling_unit_name" placeholder="Polling Unit Name"
                        class="border border-gray-300 rounded-lg p-3 w-full" required />

                    <!-- Polling Unit Description -->
                    <textarea name="polling_unit_description" placeholder="Polling Unit Description"
                        class="border border-gray-300 rounded-lg p-3 w-full"></textarea>

                    <!-- Latitude -->
                    <input type="text" name="lat" placeholder="Latitude"
                        class="border border-gray-300 rounded-lg p-3 w-full" />

                    <!-- Longitude -->
                    <input type="text" name="long" placeholder="Longitude"
                        class="border border-gray-300 rounded-lg p-3 w-full" />

                    <!-- Entered By User -->
                    <input type="text" name="entered_by_user" placeholder="Entered By User"
                        class="border border-gray-300 rounded-lg p-3 w-full" required />
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Party Scores</h3>
                    <table class="w-full border-collapse border border-gray-300 text-sm">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="border p-3">Party</th>
                                <th class="border p-3">Score</th>
                                <th class="border p-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="partyScoresTableBody">
                            <!-- Rows will be generated here -->
                        </tbody>
                    </table>
                    <button type="button" id="addPartyRowButton"
                        class="mt-3 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                        Add Party
                    </button>
                </div>

                <!-- Submit Button -->
                <div class="text-right">
                    <button type="submit"
                        class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                        Submit Result
                    </button>
                </div>
            </form>
        </div>

        <!-- Modal -->
        <div id="resultModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Polling Unit Results</h2>
                <div id="modalContent"></div>
                <button onclick="closeModal()" class="mt-4 px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                    Close
                </button>
            </div>
        </div>
    </div>



    <!-- Tailwind JS -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Initialize dropdowns
            initializeDropdowns();
            loadParties();
            fetchStates('state');




            let partyList = [];

            // Load parties from backend
            async function loadParties() {
                try {
                    const response = await fetch('/polling-units/parties', {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) throw new Error(`HTTP ${response.status}`);

                    partyList = await response.json();
                    renderPartyRows();

                } catch (err) {
                    console.error('Failed to load parties:', err);
                }
            }

            // Create a table row for a given party
            function createPartyRow(party = null) {
                const partyOptions = partyList.map(p =>
                    `<option value="${p.partyid}" ${party === p.partyid ? 'selected' : ''}>${p.partyid}</option>`
                ).join('');

                const row = document.createElement('tr');
                row.innerHTML = `
            <td class="border p-3">
                <select name="party[]" class="partyDropdown border border-gray-300 rounded-lg p-2 w-full" required>
                    <option value="">Select Party</option>
                    ${partyOptions}
                </select>
            </td>
            <td class="border p-3">
                <input type="number" name="score[]" placeholder="Score"
                    class="border border-gray-300 rounded-lg p-2 w-full" required />
            </td>
            <td class="border p-3 text-center">
                <button type="button"
                    class="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600 removePartyRowButton">
                    Remove
                </button>
            </td>
        `;
                return row;
            }

            // Render one row per party on initial load
            function renderPartyRows() {
                const tbody = document.getElementById('partyScoresTableBody');
                tbody.innerHTML = ''; // Clear any old rows

                partyList.forEach(p => {
                    const row = createPartyRow(p.partyid);
                    tbody.appendChild(row);
                });
            }

            // Add a new blank row (reuses existing party list)
            function addNewPartyRow() {
                const tbody = document.getElementById('partyScoresTableBody');
                const row = createPartyRow();
                tbody.appendChild(row);
            }

            // Handle remove button clicks (event delegation)
            document.addEventListener('click', function (e) {
                if (e.target.classList.contains('removePartyRowButton')) {
                    e.target.closest('tr').remove();
                }
            });

            // Add row button click
            document.getElementById('addPartyRowButton').addEventListener('click', addNewPartyRow);







            // Event listeners for dropdowns
            setupDropdownListeners();

            document.getElementById('state').addEventListener('change', function () {
                const stateId = this.value;
                if (stateId) {
                    fetchLGAs(stateId, 'lga');
                } else {
                    resetDropdown('lga', 'Select LGA');
                    resetDropdown('ward', 'Select Ward');
                }
            });

            document.getElementById('lga').addEventListener('change', function () {
                const lgaId = this.value;
                if (lgaId) {
                    fetchWards(lgaId, 'ward');
                } else {
                    resetDropdown('ward', 'Select Ward');
                }
            });

            // Add new party row
            document.getElementById('addPartyRowButton').addEventListener('click', function () {
                const tableBody = document.getElementById('partyScoresTableBody');
                const row = `
                    <tr>
                        <td class="border p-3">
                            <input type="text" name="party[]" placeholder="Party Abbreviation" class="border border-gray-300 rounded-lg p-2 w-full" required />
                        </td>
                        <td class="border p-3">
                            <input type="number" name="score[]" placeholder="Score" class="border border-gray-300 rounded-lg p-2 w-full" required />
                        </td>
                        <td class="border p-3 text-center">
                            <button type="button" class="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600 removePartyRowButton">
                                Remove
                            </button>
                        </td>
                    </tr>
                `;
                tableBody.insertAdjacentHTML('beforeend', row);
            });

            // Remove party row
            document.getElementById('partyScoresTableBody').addEventListener('click', function (e) {
                if (e.target.classList.contains('removePartyRowButton')) {
                    e.target.closest('tr').remove();
                }
            });

            // Toggle LGA input
            document.getElementById('addLgaButton').addEventListener('click', function () {
                const lgaNameInput = document.getElementById('lga_name');
                lgaNameInput.classList.toggle('hidden');

                // Optional: Clear LGA select if showing name input
                if (!lgaNameInput.classList.contains('hidden')) {
                    document.getElementById('lga').value = '';
                }
            });

            // Toggle Ward input
            document.getElementById('addWardButton').addEventListener('click', function () {
                const wardNameInput = document.getElementById('ward_name');
                wardNameInput.classList.toggle('hidden');

                // Optional: Clear ward select if showing name input
                if (!wardNameInput.classList.contains('hidden')) {
                    document.getElementById('ward').value = '';
                }
            });

            // Event listeners for forms
            setupFormListeners();

            // Modal event listeners
            setupModalListeners();
        });

        function initializeDropdowns() {
            fetchStates('filterState');
            fetchStates('lgaState');
        }

        function setupDropdownListeners() {
            // Filter form dropdowns
            document.getElementById('filterState').addEventListener('change', function () {
                const stateId = this.value;
                if (stateId) {
                    fetchLGAs(stateId, 'filterLga');
                } else {
                    resetDropdown('filterLga', 'Select LGA');
                    resetDropdown('filterWard', 'Select Ward');
                    resetDropdown('filterPollingUnit', 'Select Polling Unit');
                }
            });

            document.getElementById('filterLga').addEventListener('change', function () {
                const lgaId = this.value;
                if (lgaId) {
                    fetchWards(lgaId, 'filterWard');
                } else {
                    resetDropdown('filterWard', 'Select Ward');
                    resetDropdown('filterPollingUnit', 'Select Polling Unit');
                }
            });

            document.getElementById('filterWard').addEventListener('change', function () {
                const wardId = this.value;
                if (wardId) {
                    fetchPollingUnits(wardId, 'filterPollingUnit');
                } else {
                    resetDropdown('filterPollingUnit', 'Select Polling Unit');
                }
            });

            // LGA results form dropdowns
            document.getElementById('lgaState').addEventListener('change', function () {
                const stateId = this.value;
                if (stateId) {
                    fetchLGAs(stateId, 'lgaLga');
                } else {
                    resetDropdown('lgaLga', 'Select LGA');
                }
            });
        }

        function setupFormListeners() {
            // Filter form submission
            document.getElementById('filterPollingUnitsForm').addEventListener('submit', async function (e) {
                e.preventDefault();

                // Reset the filtered table before fetching new data
                resetFilteredTable();

                // Get the form data
                const formData = new FormData(this);
                const params = new URLSearchParams(formData);
                const response = await fetch(`/polling-units/filter?${params.toString()}`, { cache: 'no-store' });

                try {
                    // Fetch filtered results from the backend
                    if (response.ok) {
                        const data = await response.json();
                        renderFilteredResults(data);
                    } else {
                        console.error("Error fetching filtered results:", response.statusText);
                        alert("Failed to fetch results. Please try again.");
                    }
                } catch (error) {
                    console.error("Error:", error);
                }
            });

            // LGA results form submission
            document.getElementById('lgaResultsForm').addEventListener('submit', async function (e) {
                e.preventDefault();
                const lgaId = document.getElementById('lgaLga').value;
                if (!lgaId) {
                    alert("Please select a valid LGA");
                    return;
                }
                try {
                    const response = await fetch(`/polling-units/results?lgaId=${lgaId}`);
                    if (response.ok) {
                        const data = await response.json();
                        renderLgaResults(data);
                    } else {
                        throw new Error("Failed to fetch LGA results");
                    }
                } catch (error) {
                    console.error("Error fetching LGA results:", error);
                }
            });

            document.getElementById('addPollingUnitForm').addEventListener('submit', async function (e) {
                e.preventDefault();

                const form = e.target;

                // Step 1: Collect Static Fields
                const stateId = document.getElementById('state').value;
                const lgaId = document.getElementById('lga').value;
                const lgaName = document.getElementById('lga_name')?.value ?? null;

                const wardId = document.getElementById('ward').value;
                const wardName = document.getElementById('ward_name')?.value ?? null;

                const pollingUnitId = form.polling_unit_id.value;
                const pollingUnitNumber = form.polling_unit_number.value;
                const pollingUnitName = form.polling_unit_name.value;
                const pollingUnitDescription = form.polling_unit_description.value;
                const lat = form.lat.value;
                const long = form.long.value;
                const enteredByUser = form.entered_by_user.value;

                // Step 2: Collect Dynamic Party Scores
                const partyScores = [];
                const partyRows = document.querySelectorAll('#partyScoresTableBody tr');

                partyRows.forEach(row => {
                    const partySelect = row.querySelector('select');
                    const scoreInput = row.querySelector('input[type="number"]');

                    const partyId = partySelect.value;
                    const partyName = partySelect.options[partySelect.selectedIndex]?.text ?? '';

                    if (partyId && scoreInput.value) {
                        partyScores.push({
                            party_id: partyId,
                            party_name: partyId !== partyName ? partyName : null, // if name differs, treat as new
                            score: parseInt(scoreInput.value)
                        });
                    }
                });

                // Step 3: Build the Payload
                const payload = {
                    state_id: stateId,
                    lga_id: lgaId || null,
                    lga_name: !lgaId && lgaName ? lgaName : null,
                    ward_id: wardId || null,
                    ward_name: !wardId && wardName ? wardName : null,
                    polling_unit_id: pollingUnitId,
                    polling_unit_number: pollingUnitNumber,
                    polling_unit_name: pollingUnitName,
                    polling_unit_description: pollingUnitDescription,
                    lat: lat,
                    long: long,
                    entered_by_user: enteredByUser,
                    party_scores: partyScores
                };

                // Step 4: Send via fetch
                try {
                    const response = await fetch('/polling-units/store', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });

                    const result = await response.json();

                    if (response.ok) {
                        alert('Polling unit result submitted successfully!');
                        form.reset(); // Optional: clear the form
                    } else {
                        console.error('Error:', result);
                        alert(result.message || 'An error occurred while submitting.');
                    }
                } catch (error) {
                    console.error('Submit failed:', error);
                    alert('Failed to submit. Please try again.');
                }
            });


        }

        function setupModalListeners() {
            document.getElementById('resultModal').addEventListener('click', function (event) {
                if (event.target === this) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeModal();
                }
            });
        }

        async function fetchStates(targetId) {
            try {
                const response = await fetch('/polling-units/states');
                const states = await response.json();
                populateDropdown(targetId, states, 'state_id', 'state_name', 'Select State');
            } catch (error) {
                console.error("Error fetching states:", error);
            }
        }

        async function fetchLGAs(stateId, targetId) {
            try {
                const response = await fetch(`/polling-units/lgas?state_id=${stateId}`);
                const lgas = await response.json();
                populateDropdown(targetId, lgas, 'lga_id', 'lga_name', 'Select LGA');
            } catch (error) {
                console.error("Error fetching LGAs:", error);
            }
        }

        async function fetchWards(lgaId, targetId) {
            try {
                const response = await fetch(`/polling-units/wards?lga_id=${lgaId}`);
                const wards = await response.json();
                populateDropdown(targetId, wards, 'ward_id', 'ward_name', 'Select Ward');
            } catch (error) {
                console.error("Error fetching wards:", error);
            }
        }

        async function fetchPollingUnits(wardId, targetId) {
            const response = await fetch(`/polling-units/pu?ward_id=${wardId}`);
            const pollingUnits = await response.json();
            populateDropdown(targetId, pollingUnits, 'uniqueid', 'polling_unit_name', 'Select Polling Unit');
        }

        function populateDropdown(targetId, items, valueKey, textKey, defaultText) {
            const select = document.getElementById(targetId);
            resetDropdown(targetId, defaultText);
            items.forEach(item => {
                const option = document.createElement('option');
                option.value = item[valueKey]; // This is the uniqueid
                option.textContent = item[textKey];
                select.appendChild(option);
            });
            select.disabled = false;
        }

        function resetDropdown(selectId, placeholder) {
            const select = document.getElementById(selectId);
            select.innerHTML = `<option value="">${placeholder}</option>`;
            select.disabled = true;
        }

        function resetFilteredTable() {
            const tableBody = document.getElementById('filteredTableBody');
            const resultsTable = document.getElementById('filteredResultsTable');

            // Clear the table body
            tableBody.innerHTML = '';

            // Hide the results table
            resultsTable.classList.add('hidden');
        }

        function renderFilteredResults(data) {
            const tableBody = document.getElementById('filteredTableBody');
            const resultsTable = document.getElementById('filteredResultsTable');

            // Clear the table body
            tableBody.innerHTML = '';

            if (data.data && data.data.length > 0) {
                // Populate the table with results
                data.data.forEach(result => {
                    const row = `
                    <tr>
                        <td class="border p-2">${result.party_abbreviation}</td>
                        <td class="border p-2">${result.party_score}</td>
                        <td class="border p-2">${result.polling_unit?.polling_unit_name || 'N/A'}</td>
                        <td class="border p-2">${result.polling_unit?.ward?.ward_name || 'N/A'}</td>
                        <td class="border p-2">${result.polling_unit?.ward?.lga?.lga_name || 'N/A'}</td>
                        <td class="border p-2">${result.polling_unit?.ward?.lga?.state?.state_name || 'N/A'}</td>
                        <td class="border p-2">${result.entered_by_user}</td>
                        <td class="border p-2">${new Date(result.date_entered).toLocaleString()}</td>
                    </tr>
                `;
                    tableBody.insertAdjacentHTML('beforeend', row);
                });

                // Show the results table
                resultsTable.classList.remove('hidden');
            } else {
                // Display a message inside the table or as an alert
                tableBody.innerHTML = `<tr><td colspan="8" class="p-4 text-center text-gray-500">No polling results found for the selected polling unit.</td></tr>`;
                resultsTable.classList.remove('hidden'); // Ensure the table is visible even if empty
            }
        }

        function renderLgaResults(data) {
            const tableBody = document.getElementById('lgaResultsTableBody');
            const header = document.getElementById('lgaResultsHeader');
            tableBody.innerHTML = '';
            if (data.summed_results && Object.keys(data.summed_results).length > 0) {
                for (const [party, summedScore] of Object.entries(data.summed_results)) {
                    const announcedScore = data.announced_results[party] || 0;
                    const row = `
                    <tr>
                        <td class="border p-2">${party}</td>
                        <td class="border p-2">${summedScore}</td>
                        <td class="border p-2">${announcedScore}</td>
                    </tr>
                `;
                    tableBody.insertAdjacentHTML('beforeend', row);
                }
                const selectedLga = document.getElementById('lgaLga').options[document.getElementById('lgaLga').selectedIndex].text;
                header.textContent = `Summed Results for LGA: ${selectedLga}`;
            } else {
                tableBody.innerHTML = `<tr><td colspan="3" class="p-4 text-center text-gray-500">No results found.</td></tr>`;
                header.textContent = 'Summed Results for LGA:';
            }
            document.getElementById('lgaResultsContainer').classList.remove('hidden');
        }

        function openModal(unit) {
            const modalContent = document.getElementById('modalContent');
            const winningPartyHtml = unit.winning_result?.party
                ? `Winning Party: ${unit.winning_result.party} (${unit.winning_result.score})`
                : 'No winning result available';
            const partyScores = unit.results.map(r => {
                const isWinner = unit.winning_result && r.party === unit.winning_result.party;
                return `<li class="${isWinner ? 'text-green-600 font-bold' : ''}">${r.party}: ${r.score}</li>`;
            }).join('');
            modalContent.innerHTML = `
            <p><strong>Polling Unit:</strong> ${unit.polling_unit_name}</p>
            <p><strong>Entered By:</strong> ${unit.entered_by_user}</p>
            <p><strong>Agent(s):</strong> ${unit.agent_names}</p>
            <h3 class="mt-4 font-semibold">Party Scores:</h3>
            <ul class="list-disc pl-6">${partyScores}</ul>
            <p class="mt-4 font-semibold text-lg">${winningPartyHtml}</p>
        `;
            document.getElementById('resultModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('resultModal').classList.add('hidden');
        }
    </script>

</body>



</html>