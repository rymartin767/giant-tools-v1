<?php

namespace App\Livewire;

use App\Services\AviationWeatherService;
use App\Services\GiantsService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Flight extends Component
{
    public $employeeNumber = '';

    public $departureAirport = '';

    public $arrivalAirport = '';

    public $etops = false;

    public $liveAnimals = false;

    public $hazmat = false;

    public $coldWeatherOps = false;

    public $llws = false;

    public function mount()
    {
        $this->loadEmployeeNumber();

        if ($this->employeeNumber) {
            $this->loadFlightDetails();
        }
    }

    private function loadEmployeeNumber()
    {
        $sessionKey = session()->getId();
        $cacheKey = "employee_number_{$sessionKey}";

        $this->employeeNumber = Cache::get($cacheKey, '');
    }

    public function getAirportOptionsProperty(): array
    {
        return $this->getAirports();
    }

    public function getDepartureMetarProperty(): ?array
    {
        if (empty($this->departureAirport)) {
            return null;
        }

        $weatherService = app(AviationWeatherService::class);
        $response = $weatherService->getMetar($this->departureAirport);

        if (! $response['success'] || empty($response['data'])) {
            return null;
        }

        $metarData = $response['data'][0] ?? null;

        if (! $metarData) {
            return null;
        }

        return [
            'raw' => $metarData['rawOb'] ?? 'No METAR available',
            'flightCategory' => $weatherService->parseFlightCategory($metarData['fltCat'] ?? null),
        ];
    }

    public function getArrivalTafProperty(): ?array
    {
        if (empty($this->arrivalAirport)) {
            return null;
        }

        $weatherService = app(AviationWeatherService::class);
        $response = $weatherService->getTaf($this->arrivalAirport);

        if (! $response['success'] || empty($response['data'])) {
            return null;
        }

        $tafData = $response['data'][0] ?? null;

        if (! $tafData) {
            return null;
        }

        return [
            'raw' => $tafData['rawTAF'] ?? 'No TAF available',
        ];
    }

    private function loadFlightDetails()
    {
        if (! $this->employeeNumber) {
            return;
        }

        $cacheKey = "flight_details_{$this->employeeNumber}";

        $config = Cache::get($cacheKey, []);
        $this->departureAirport = $config['departureAirport'] ?? '';
        $this->arrivalAirport = $config['arrivalAirport'] ?? '';
        $this->etops = $config['etops'] ?? false;
        $this->liveAnimals = $config['liveAnimals'] ?? false;
        $this->hazmat = $config['hazmat'] ?? false;
        $this->coldWeatherOps = $config['coldWeatherOps'] ?? false;
        $this->llws = $config['llws'] ?? false;
    }

    public function saveFlightConfiguration()
    {
        if (! $this->employeeNumber) {
            session()->flash('error', 'Employee number is required. Please complete the Trip step first.');

            return;
        }

        $airportKeys = array_keys($this->airportOptions);

        $this->validate([
            'departureAirport' => 'required|in:'.implode(',', $airportKeys),
            'arrivalAirport' => 'required|in:'.implode(',', $airportKeys),
        ], [
            'departureAirport.required' => 'Please select a departure airport.',
            'departureAirport.in' => 'Please select a valid departure airport.',
            'arrivalAirport.required' => 'Please select an arrival airport.',
            'arrivalAirport.in' => 'Please select a valid arrival airport.',
        ]);

        // Save to cache
        $cacheKey = "flight_details_{$this->employeeNumber}";

        $config = [
            'departureAirport' => $this->departureAirport,
            'arrivalAirport' => $this->arrivalAirport,
            'etops' => $this->etops,
            'liveAnimals' => $this->liveAnimals,
            'hazmat' => $this->hazmat,
            'coldWeatherOps' => $this->coldWeatherOps,
            'llws' => $this->llws,
        ];

        Cache::put($cacheKey, $config, now()->addDays(30));

        session()->flash('message', 'Flight configuration saved successfully.');
    }

    public function clearFlight()
    {
        if (! $this->employeeNumber) {
            return;
        }

        $this->departureAirport = '';
        $this->arrivalAirport = '';
        $this->etops = false;
        $this->liveAnimals = false;
        $this->hazmat = false;
        $this->coldWeatherOps = false;
        $this->llws = false;

        // Clear from cache
        $cacheKey = "flight_details_{$this->employeeNumber}";

        Cache::forget($cacheKey);
        Cache::flush();

        session()->flash('message', 'Flight configuration cleared successfully.');
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
                ->when($search, fn ($collection, $search) => $collection->filter(fn ($icao) => str_contains(strtolower($icao), strtolower($search))))
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
        return view('livewire.flight');
    }
}
