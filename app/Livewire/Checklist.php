<?php

namespace App\Livewire;

use App\Data\AirportNotes;
use App\Data\ChecklistSteps;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Checklist extends Component
{
    public $currentStep = '1.0';

    public $steps;

    public $completedItems = [];

    public $stepKeys = [];

    public $totalSteps;

    // Flight Details properties
    public $engines = '';

    public $config = '';

    public $supernumeraries = false;

    public $departureAirport = '';

    public $arrivalAirport = '';

    public $flightDetailsComplete = false;

    // Flight Details options
    public $engineOptions = ['GE', 'PW'];

    public $configOptions = ['PAX', 'FRT'];

    public $airportOptions = ['KJFK', 'KLAX', 'KORD', 'KATL', 'KDEN', 'KDFW', 'KIAH', 'KPHX', 'KCLT', 'KEWR'];

    public function mount()
    {
        // Load steps from external data file
        $this->steps = ChecklistSteps::getSteps();

        // Get step keys for navigation - ensure we preserve decimal keys
        $this->stepKeys = array_keys($this->steps);
        $this->totalSteps = count($this->stepKeys);

        // Load cached completed items for the authenticated user
        $this->loadCompletedItems();

        // Load cached flight details
        $this->loadFlightDetails();
    }

    private function loadFlightDetails()
    {
        $userId = Auth::id();
        $cacheKey = "checklist_flight_details_{$userId}";

        $config = Cache::get($cacheKey, []);
        $this->engines = $config['engines'] ?? '';
        $this->config = $config['config'] ?? '';
        $this->supernumeraries = $config['supernumeraries'] ?? false;
        $this->departureAirport = $config['departureAirport'] ?? '';
        $this->arrivalAirport = $config['arrivalAirport'] ?? '';
        $this->flightDetailsComplete = ! empty($this->engines) && ! empty($this->config) && ! empty($this->departureAirport) && ! empty($this->arrivalAirport);
    }

    private function saveFlightDetails()
    {
        $userId = Auth::id();
        $cacheKey = "checklist_flight_details_{$userId}";

        $config = [
            'engines' => $this->engines,
            'config' => $this->config,
            'supernumeraries' => $this->supernumeraries,
            'departureAirport' => $this->departureAirport,
            'arrivalAirport' => $this->arrivalAirport,
        ];

        Cache::put($cacheKey, $config, now()->addDays(30));
    }

    public function completeFlightDetails()
    {
        $this->validate([
            'engines' => 'required|in:GE,PW',
            'config' => 'required|in:PAX,FRT',
            'departureAirport' => 'required|in:'.implode(',', $this->airportOptions),
            'arrivalAirport' => 'required|in:'.implode(',', $this->airportOptions),
        ], [
            'engines.required' => 'Please select an engine type.',
            'engines.in' => 'Please select either GE or PW engines.',
            'config.required' => 'Please select a configuration.',
            'config.in' => 'Please select either PAX or FRT configuration.',
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
        $this->engines = '';
        $this->config = '';
        $this->supernumeraries = false;
        $this->departureAirport = '';
        $this->arrivalAirport = '';
        $this->flightDetailsComplete = false;
        $this->saveFlightDetails();

        // Also reset the checklist since flight details changed
        $this->resetChecklist();
    }

    public function updatedSupernumeraries()
    {
        $this->saveFlightDetails();
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
        $userId = Auth::id();
        $cacheKey = "checklist_completed_items_{$userId}";

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
        $userId = Auth::id();
        $cacheKey = "checklist_completed_items_{$userId}";

        // Cache for 30 days (or until manually reset)
        Cache::put($cacheKey, $this->completedItems, now()->addDays(30));
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
                // New conditional item format
                if ($this->itemMatchesConditions($item['conditions'])) {
                    $filteredItems[$index] = $item['text'];
                }
            } else {
                // Legacy string format - always include
                $filteredItems[$index] = $item;
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
            $currentValue = $this->{$field};

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
        $userId = Auth::id();
        $cacheKey = "checklist_completed_items_{$userId}";
        Cache::forget($cacheKey);
    }

    public function render()
    {
        if (! $this->flightDetailsComplete) {
            return view('livewire.checklist.flight-details');
        }

        return view('livewire.checklist.main');
    }
}
