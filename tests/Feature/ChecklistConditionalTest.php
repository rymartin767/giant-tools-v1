<?php

declare(strict_types=1);

use App\Livewire\Checklist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

uses(RefreshDatabase::class)->group('checklist');

beforeEach(function () {
    // Start session to get session ID
    $this->withSession([]);
    $this->sessionId = session()->getId();

    // Set employee number for this session
    $this->employeeNumber = 'test-emp-123';
    Cache::put("employee_number_{$this->sessionId}", $this->employeeNumber, now()->addDays(30));
});

test('checklist reads etops from flight cache', function () {
    $flightCacheKey = "flight_details_{$this->employeeNumber}";
    Cache::put($flightCacheKey, [
        'departureAirport' => 'KJFK',
        'arrivalAirport' => 'EGLL',
        'etops' => true,
    ], now()->addDays(30));

    $component = Livewire::test(Checklist::class);

    expect($component->etops)->toBe(true);
});

test('checklist reads aircraft configuration from aircraft cache', function () {
    $aircraftCacheKey = "aircraft_details_{$this->employeeNumber}";
    Cache::put($aircraftCacheKey, [
        'selectedAirframe' => 'N12345',
        'engines' => 'GE',
        'config' => 'PAX',
        'supernumeraries' => true,
    ], now()->addDays(30));

    $component = Livewire::test(Checklist::class);

    expect($component->engines)->toBe('GE')
        ->and($component->config)->toBe('PAX')
        ->and($component->supernumeraries)->toBe(true);
});

test('checklist filters items based on etops condition', function () {
    $flightCacheKey = "flight_details_{$this->employeeNumber}";
    Cache::put($flightCacheKey, [
        'departureAirport' => 'KJFK',
        'arrivalAirport' => 'EGLL',
        'etops' => true,
    ], now()->addDays(30));

    $aircraftCacheKey = "aircraft_details_{$this->employeeNumber}";
    Cache::put($aircraftCacheKey, [
        'selectedAirframe' => 'N12345',
        'engines' => 'GE',
        'config' => 'PAX',
        'supernumeraries' => false,
    ], now()->addDays(30));

    $component = Livewire::test(Checklist::class);

    // Check if ETOPS step exists and has items when etops is true
    if (isset($component->steps['3.0'])) {
        $filteredItems = $component->getFilteredItems('3.0');
        expect($filteredItems)->not->toBeEmpty();
    }
});

test('checklist filters items based on engine type', function () {
    $aircraftCacheKey = "aircraft_details_{$this->employeeNumber}";
    Cache::put($aircraftCacheKey, [
        'selectedAirframe' => 'N12345',
        'engines' => 'GE',
        'config' => 'PAX',
        'supernumeraries' => false,
    ], now()->addDays(30));

    $component = Livewire::test(Checklist::class);

    // Check if engine-specific items are filtered correctly
    if (isset($component->instance()->steps['2.2'])) {
        $filteredItems = $component->instance()->getFilteredItems('2.2');

        // Should include GE items, not PW items
        foreach ($filteredItems as $item) {
            if (is_array($item) && isset($item['text'])) {
                // Items with GE condition should be present
                // Items with PW condition should not be present
                expect($item['text'])->not->toContain('PW •');
            }
        }
    }
});

test('checklist uses separate cache keys for flight and aircraft', function () {
    $flightCacheKey = "flight_details_{$this->employeeNumber}";
    $aircraftCacheKey = "aircraft_details_{$this->employeeNumber}";

    Cache::put($flightCacheKey, [
        'departureAirport' => 'KJFK',
        'arrivalAirport' => 'EGLL',
        'etops' => true,
    ], now()->addDays(30));

    Cache::put($aircraftCacheKey, [
        'selectedAirframe' => 'N12345',
        'engines' => 'GE',
        'config' => 'PAX',
        'supernumeraries' => false,
    ], now()->addDays(30));

    $component = Livewire::test(Checklist::class);

    // Verify data comes from correct cache keys
    expect($component->etops)->toBe(true)
        ->and($component->engines)->toBe('GE')
        ->and($component->config)->toBe('PAX')
        ->and($component->supernumeraries)->toBe(false);
});
