<?php

namespace App\Livewire;

use App\Data\AirportNotes;
use App\Data\ChecklistSteps;
use App\Services\GiantsService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Checklist extends Component
{
    public $employeeNumber = '';

    public $currentStep = '1.0';

    public $steps;

    public $completedItems = [];

    public $stepKeys = [];

    public $totalSteps;

    // Flight Details properties
    public $departureAirport = '';

    public $arrivalAirport = '';

    public $flightDetailsComplete = false;

    public $aircraftConfigComplete = false;

    protected $listeners = ['item-toggled' => 'handleItemToggled'];

    public function mount()
    {
        $this->loadEmployeeNumber();

        // Load steps from external data file
        $this->steps = ChecklistSteps::getSteps();

        // Get step keys for navigation - ensure we preserve decimal keys
        $this->stepKeys = array_keys($this->steps);
        $this->totalSteps = count($this->stepKeys);

        if ($this->employeeNumber) {
            // Load cached completed items for the authenticated user
            $this->loadCompletedItems();

            // Load cached flight details
            $this->loadFlightDetails();

            // Check aircraft configuration
            $this->checkAircraftConfig();
        }
    }

    private function loadEmployeeNumber()
    {
        $sessionKey = session()->getId();
        $cacheKey = "employee_number_{$sessionKey}";

        $this->employeeNumber = Cache::get($cacheKey, '');
    }

    private function checkAircraftConfig()
    {
        if (! $this->employeeNumber) {
            $this->aircraftConfigComplete = false;

            return;
        }

        $cacheKey = "aircraft_details_{$this->employeeNumber}";

        $config = Cache::get($cacheKey, []);
        $this->aircraftConfigComplete = ! empty($config['selectedAirframe']);
    }

    public function getAirportOptionsProperty(): array
    {
        return $this->getAirports();
    }

    private function loadFlightDetails()
    {
        // employeeNumber used instead
        $cacheKey = "flight_details_{$this->employeeNumber}";

        $config = Cache::get($cacheKey, []);
        $this->departureAirport = $config['departureAirport'] ?? '';
        $this->arrivalAirport = $config['arrivalAirport'] ?? '';
        $this->flightDetailsComplete = ! empty($this->departureAirport) && ! empty($this->arrivalAirport);
    }

    #[Computed]
    public function engines()
    {
        // employeeNumber used instead
        $cacheKey = "aircraft_details_{$this->employeeNumber}";
        $config = Cache::get($cacheKey, []);

        return $config['engines'] ?? '';
    }

    #[Computed]
    public function config()
    {
        // employeeNumber used instead
        $cacheKey = "aircraft_details_{$this->employeeNumber}";
        $config = Cache::get($cacheKey, []);

        return $config['config'] ?? '';
    }

    #[Computed]
    public function supernumeraries()
    {
        // employeeNumber used instead
        $cacheKey = "aircraft_details_{$this->employeeNumber}";
        $config = Cache::get($cacheKey, []);

        return $config['supernumeraries'] ?? false;
    }

    #[Computed]
    public function etops()
    {
        // employeeNumber used instead
        $cacheKey = "flight_details_{$this->employeeNumber}";
        $config = Cache::get($cacheKey, []);

        return $config['etops'] ?? false;
    }

    private function saveFlightDetails()
    {
        // employeeNumber used instead
        $cacheKey = "checklist_flight_details_{$this->employeeNumber}";

        // Preserve existing aircraft configuration
        $existingConfig = Cache::get($cacheKey, []);

        $config = [
            'selectedAirframe' => $existingConfig['selectedAirframe'] ?? '',
            'engines' => $existingConfig['engines'] ?? '',
            'config' => $existingConfig['config'] ?? '',
            'supernumeraries' => $existingConfig['supernumeraries'] ?? false,
            'departureAirport' => $this->departureAirport,
            'arrivalAirport' => $this->arrivalAirport,
        ];

        Cache::put($cacheKey, $config, now()->addDays(30));
    }

    public function completeFlightDetails()
    {
        $this->validate([
            'departureAirport' => 'required|in:'.implode(',', $this->airportOptions),
            'arrivalAirport' => 'required|in:'.implode(',', $this->airportOptions),
        ], [
            'departureAirport.required' => 'Please select a departure airport.',
            'departureAirport.in' => 'Please select a valid departure airport.',
            'arrivalAirport.required' => 'Please select an arrival airport.',
            'arrivalAirport.in' => 'Please select a valid arrival airport.',
        ]);

        $this->flightDetailsComplete = true;
        $this->saveFlightDetails();
    }

    public function resetFlightDetails()
    {
        $this->departureAirport = '';
        $this->arrivalAirport = '';
        $this->flightDetailsComplete = false;
        $this->saveFlightDetails();

        // Also reset the checklist since flight details changed
        $this->resetChecklist();
    }

    public function updatedDepartureAirport()
    {
        $this->saveFlightDetails();
    }

    public function updatedArrivalAirport()
    {
        $this->saveFlightDetails();
    }

    public function getAirportNotes($airportCode)
    {
        return AirportNotes::getDepartureNotes($airportCode);
    }

    private function loadCompletedItems()
    {
        // employeeNumber used instead
        $cacheKey = "checklist_completed_items_{$this->employeeNumber}";

        $this->completedItems = Cache::get($cacheKey, []);

        // Initialize empty arrays for steps that don't have cached data
        foreach ($this->stepKeys as $stepKey) {
            if (! isset($this->completedItems[$stepKey])) {
                $this->completedItems[$stepKey] = [];
            }
        }
    }

    private function saveCompletedItems()
    {
        // employeeNumber used instead
        $cacheKey = "checklist_completed_items_{$this->employeeNumber}";

        // Cache for 30 days (or until manually reset)
        Cache::put($cacheKey, $this->completedItems, now()->addDays(30));
    }

    public function handleItemToggled($data)
    {
        $stepKey = $data['stepKey'];
        $itemIndex = $data['itemIndex'];
        $completed = $data['completed'];

        if (! isset($this->completedItems[$stepKey])) {
            $this->completedItems[$stepKey] = [];
        }

        if ($completed) {
            // Add to completed items if not already there
            if (! in_array($itemIndex, $this->completedItems[$stepKey])) {
                $this->completedItems[$stepKey][] = $itemIndex;
            }
        } else {
            // Remove from completed items
            $this->completedItems[$stepKey] = array_diff($this->completedItems[$stepKey], [$itemIndex]);
        }

        // Save to cache after each toggle
        $this->saveCompletedItems();
    }

    public function toggleItem($stepKey, $itemIndex)
    {
        if (! isset($this->completedItems[$stepKey])) {
            $this->completedItems[$stepKey] = [];
        }

        if (in_array($itemIndex, $this->completedItems[$stepKey])) {
            $this->completedItems[$stepKey] = array_diff($this->completedItems[$stepKey], [$itemIndex]);
        } else {
            $this->completedItems[$stepKey][] = $itemIndex;
        }

        // Save to cache after each toggle
        $this->saveCompletedItems();

        // Trigger haptic feedback and offline sync
        $this->dispatch('item-toggled', [
            'stepKey' => $stepKey,
            'itemIndex' => $itemIndex,
            'completed' => in_array($itemIndex, $this->completedItems[$stepKey]),
        ]);
    }

    public function isStepComplete($stepKey)
    {
        $items = $this->getFilteredItems($stepKey);
        $totalItems = count($items);
        $completedCount = isset($this->completedItems[$stepKey]) ? count($this->completedItems[$stepKey]) : 0;

        return $completedCount === $totalItems;
    }

    public function getFilteredItems($stepKey)
    {
        $items = $this->steps[$stepKey]['items'];
        $filteredItems = [];

        foreach ($items as $index => $item) {
            if (is_array($item)) {
                // Check if it has conditions (conditional item) or notes (item with notes)
                if (isset($item['conditions'])) {
                    // New conditional item format
                    if ($this->itemMatchesConditions($item['conditions'])) {
                        $filteredItems[$index] = [
                            'text' => $item['text'],
                            'notes' => $item['notes'] ?? null,
                            'images' => $item['images'] ?? null,
                            'subitems' => $item['subitems'] ?? null,
                        ];
                    }
                } else {
                    // Item with notes, images, subitems, or combination (no conditions)
                    $filteredItems[$index] = [
                        'text' => $item['text'],
                        'notes' => $item['notes'] ?? null,
                        'images' => $item['images'] ?? null,
                        'subitems' => $item['subitems'] ?? null,
                    ];
                }
            } else {
                // Legacy string format - always include
                $filteredItems[$index] = [
                    'text' => $item,
                    'notes' => null,
                    'images' => null,
                ];
            }
        }

        return $filteredItems;
    }

    private function itemMatchesConditions($conditions)
    {
        if (empty($conditions)) {
            return true; // No conditions means always show
        }

        foreach ($conditions as $field => $allowedValues) {
            // Get value from cache for aircraft and flight configuration fields
            if ($field === 'engines') {
                $currentValue = $this->engines;
            } elseif ($field === 'config') {
                $currentValue = $this->config;
            } elseif ($field === 'supernumeraries') {
                $currentValue = $this->supernumeraries;
            } elseif ($field === 'etops') {
                $currentValue = $this->etops;
            } else {
                $currentValue = $this->{$field} ?? null;
            }

            if (! in_array($currentValue, $allowedValues)) {
                return false;
            }
        }

        return true;
    }

    public function nextStep()
    {
        $currentIndex = array_search($this->currentStep, $this->stepKeys);
        if ($currentIndex !== false && $currentIndex < count($this->stepKeys) - 1 && $this->isStepComplete($this->currentStep)) {
            $this->currentStep = $this->stepKeys[$currentIndex + 1];
        }
    }

    public function previousStep()
    {
        $currentIndex = array_search($this->currentStep, $this->stepKeys);
        if ($currentIndex !== false && $currentIndex > 0) {
            $this->currentStep = $this->stepKeys[$currentIndex - 1];
        }
    }

    public function goToStep($stepKey)
    {
        if (in_array($stepKey, $this->stepKeys)) {
            $this->currentStep = $stepKey;
        }
    }

    public function getProgressPercentage()
    {
        $completedSteps = 0;
        foreach ($this->stepKeys as $stepKey) {
            if ($this->isStepComplete($stepKey)) {
                $completedSteps++;
            }
        }

        return ($completedSteps / $this->totalSteps) * 100;
    }

    public function resetChecklist()
    {
        $this->completedItems = [];
        $this->currentStep = $this->stepKeys[0];

        // Clear cache for the authenticated user
        // employeeNumber used instead
        $cacheKey = "checklist_completed_items_{$this->employeeNumber}";
        Cache::forget($cacheKey);
    }

    public function getItemByIndex($stepKey, $index)
    {
        $items = $this->getFilteredItems($stepKey);

        return $items[$index] ?? null;
    }

    public function getItemText($stepKey, $index)
    {
        $item = $this->getItemByIndex($stepKey, $index);
        if (! $item) {
            return '';
        }

        return is_array($item) ? $item['text'] : $item;
    }

    public function getItemNotes($stepKey, $index)
    {
        $item = $this->getItemByIndex($stepKey, $index);
        if (! $item) {
            return null;
        }

        return is_array($item) && isset($item['notes']) ? $item['notes'] : null;
    }

    public function hasNotes($stepKey, $index)
    {
        $notes = $this->getItemNotes($stepKey, $index);

        return ! empty($notes);
    }

    public function getNotesType($stepKey, $index)
    {
        $notes = $this->getItemNotes($stepKey, $index);

        return $notes ? ($notes['type'] ?? 'text') : null;
    }

    public function getModalId($stepKey, $index)
    {
        return "modal-{$stepKey}-{$index}";
    }

    private function getAirports(string $search = ''): array
    {
        try {
            $giantsService = app(GiantsService::class);
            $response = $giantsService->fetch('airports');

            if (! $response['success']) {
                Log::warning('Failed to fetch airports from Giants API', ['error' => $response['error'] ?? 'Unknown error']);

                return [];
            }

            $airports = collect($response['data'] ?? []);

            // Extract ICAO codes and filter by search term if provided
            $icaoCodes = $airports
                ->pluck('attributes.icao')
                ->filter()
                ->when($search, fn ($collection, $search) => $collection->filter(fn ($icao) => str_contains(strtolower($icao), strtolower($search))
                )
                )
                ->sort()
                ->values()
                ->all();

            // Return empty array if no airports found
            if (empty($icaoCodes)) {
                return [];
            }

            // Ensure we have simple string values
            $icaoCodes = array_map('strval', $icaoCodes);

            // Return as key-value pairs for the select component
            return array_combine($icaoCodes, $icaoCodes);

        } catch (\Exception $e) {
            Log::error('Failed to fetch airports: '.$e->getMessage());

            return [];
        }
    }

    public function render()
    {
        if (! $this->flightDetailsComplete) {
            return view('livewire.checklist.flight-details');
        }

        if (! $this->aircraftConfigComplete) {
            return view('livewire.checklist.requires-aircraft');
        }

        return view('livewire.checklist.main');
    }
}
