<?php

namespace App\Livewire;

use App\Services\GiantsService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Aircraft extends Component
{
    public $employeeNumber = '';

    public $selectedAirframe = '';

    public $engines = '';

    public $config = '';

    public $supernumeraries = false;

    public $airframeData = null;

    public $flightConfigComplete = false;

    // Options
    public $engineOptions = ['GE', 'PW'];

    public $configOptions = ['PAX', 'FRT'];

    public function mount()
    {
        $this->loadEmployeeNumber();
        $this->checkFlightConfig();

        if ($this->flightConfigComplete) {
            $this->loadSelectedAirframe();
        }
    }

    private function loadEmployeeNumber()
    {
        $sessionKey = session()->getId();
        $cacheKey = "employee_number_{$sessionKey}";

        $this->employeeNumber = Cache::get($cacheKey, '');
    }

    private function checkFlightConfig()
    {
        if (! $this->employeeNumber) {
            $this->flightConfigComplete = false;

            return;
        }

        $cacheKey = "flight_details_{$this->employeeNumber}";

        $config = Cache::get($cacheKey, []);
        $this->flightConfigComplete = ! empty($config['departureAirport']) && ! empty($config['arrivalAirport']);
    }

    public function updatedSelectedAirframe()
    {
        if ($this->selectedAirframe) {
            $this->loadAirframeData();
        }
    }

    public function getAirframeOptionsProperty(): array
    {
        return $this->getAirframes();
    }

    private function loadSelectedAirframe()
    {
        if (! $this->employeeNumber) {
            return;
        }

        $cacheKey = "aircraft_details_{$this->employeeNumber}";

        $config = Cache::get($cacheKey, []);
        $this->selectedAirframe = $config['selectedAirframe'] ?? '';
        $this->engines = $config['engines'] ?? '';
        $this->config = $config['config'] ?? '';
        $this->supernumeraries = $config['supernumeraries'] ?? false;

        if ($this->selectedAirframe) {
            $this->loadAirframeData();
        }
    }

    private function loadAirframeData()
    {
        try {
            $giantsService = app(GiantsService::class);
            $result = $giantsService->fetch('airframes');

            if (! $result['success']) {
                Log::warning('Failed to load airframe data', $result['error'] ?? []);

                return;
            }

            $aircraft = collect($result['data'] ?? []);

            // Find the selected airframe
            $airframe = $aircraft->first(function ($item) {
                return ($item['attributes']['registration'] ?? '') === $this->selectedAirframe;
            });

            $this->airframeData = $airframe ? $airframe['attributes'] : null;

            // Auto-extract config and engines from differences if available
            if ($this->airframeData && isset($this->airframeData['differences'])) {
                $this->extractConfigurationFromDifferences($this->airframeData['differences']);
            }

        } catch (\Exception $e) {
            Log::error('Failed to load airframe data: '.$e->getMessage());
        }
    }

    private function extractConfigurationFromDifferences(string $differencesJson): void
    {
        try {
            $differences = json_decode($differencesJson, true);

            if (! $differences) {
                return;
            }

            // Extract configuration (FRT or PAX) from General.Configuration
            if (isset($differences['General']['Configuration'])) {
                $configString = $differences['General']['Configuration'];
                // Configuration might be "FRT/300SF" or "PAX/440"
                if (str_contains($configString, 'FRT')) {
                    $this->config = 'FRT';
                } elseif (str_contains($configString, 'PAX')) {
                    $this->config = 'PAX';
                }
            }

            // Extract engine type from Electric/Engines.Engine Type
            if (isset($differences['Electric/Engines']['Engine Type'])) {
                $engineType = $differences['Electric/Engines']['Engine Type'];
                // B6F = GE, other codes might be PW
                if (in_array($engineType, ['B6F', 'GE'])) {
                    $this->engines = 'GE';
                } else {
                    $this->engines = 'PW';
                }
            }

        } catch (\Exception $e) {
            Log::warning('Failed to extract configuration from differences: '.$e->getMessage());
        }
    }

    public function saveAircraftConfiguration()
    {
        if (! $this->employeeNumber) {
            session()->flash('error', 'Employee number is required. Please complete the Trip step first.');

            return;
        }

        $airframeKeys = array_keys($this->airframeOptions);

        $this->validate([
            'selectedAirframe' => 'required|in:'.implode(',', $airframeKeys),
            'engines' => 'nullable|in:GE,PW',
            'config' => 'nullable|in:PAX,FRT',
        ], [
            'selectedAirframe.required' => 'Please select an aircraft.',
            'selectedAirframe.in' => 'Please select a valid aircraft.',
            'engines.in' => 'Please select either GE or PW engines.',
            'config.in' => 'Please select either PAX or FRT configuration.',
        ]);

        // Save to cache
        $cacheKey = "aircraft_details_{$this->employeeNumber}";

        $config = [
            'selectedAirframe' => $this->selectedAirframe,
            'engines' => $this->engines,
            'config' => $this->config,
            'supernumeraries' => $this->supernumeraries,
        ];

        Cache::put($cacheKey, $config, now()->addDays(30));

        // Load the airframe data
        $this->loadAirframeData();

        session()->flash('message', 'Aircraft configuration saved successfully.');
    }

    public function clearAirframe()
    {
        if (! $this->employeeNumber) {
            return;
        }

        $this->selectedAirframe = '';
        $this->engines = '';
        $this->config = '';
        $this->supernumeraries = false;
        $this->airframeData = null;

        // Clear from cache
        $cacheKey = "aircraft_details_{$this->employeeNumber}";

        Cache::forget($cacheKey);

        session()->flash('message', 'Aircraft configuration cleared successfully.');
    }

    private function getAirframes(string $search = ''): array
    {
        try {
            $giantsService = app(GiantsService::class);
            $result = $giantsService->fetch('airframes');

            if (! $result['success']) {
                Log::warning('Failed to fetch aircraft registrations', $result['error'] ?? []);

                return [];
            }

            $aircraft = collect($result['data'] ?? []);

            // Extract registrations and filter by search term if provided
            $registrations = $aircraft
                ->pluck('attributes.registration')
                ->filter()
                ->when($search, fn ($collection, $search) => $collection->filter(fn ($registration) => str_contains(strtolower($registration), strtolower($search))))
                ->sort()
                ->values()
                ->toArray();

            // Return as key-value pairs for the select component
            return array_combine($registrations, $registrations);

        } catch (\Exception $e) {
            // Log the error and return empty array
            Log::error('Failed to fetch aircraft registrations: '.$e->getMessage());

            return [];
        }
    }

    public function render()
    {
        if (! $this->flightConfigComplete) {
            return view('livewire.aircraft-requires-flight');
        }

        return view('livewire.aircraft');
    }
}
