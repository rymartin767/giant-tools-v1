<?php

declare(strict_types=1);

use App\Livewire\Flight;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

uses(RefreshDatabase::class)->group('flight');

beforeEach(function () {
    // Start session to get session ID
    $this->withSession([]);
    $this->sessionId = session()->getId();

    // Set employee number for this session
    $this->employeeNumber = 'test-emp-123';
    Cache::put("employee_number_{$this->sessionId}", $this->employeeNumber, now()->addDays(30));

    // Mock HTTP requests to Giants API
    Http::fake([
        'api.giantpilots.com/api/v1/airports*' => Http::response([
            'data' => [
                ['attributes' => ['icao' => 'KJFK']],
                ['attributes' => ['icao' => 'EGLL']],
                ['attributes' => ['icao' => 'KSEA']],
            ],
        ], 200),
    ]);
});

test('flight component renders successfully', function () {
    Livewire::test(Flight::class)
        ->assertStatus(200);
});

test('flight component loads cached flight details', function () {
    $cacheKey = "flight_details_{$this->employeeNumber}";
    Cache::put($cacheKey, [
        'departureAirport' => 'KJFK',
        'arrivalAirport' => 'EGLL',
        'etops' => true,
    ], now()->addDays(30));

    Livewire::test(Flight::class)
        ->assertSet('departureAirport', 'KJFK')
        ->assertSet('arrivalAirport', 'EGLL')
        ->assertSet('etops', true);
});

test('flight component saves configuration to cache', function () {
    $cacheKey = "flight_details_{$this->employeeNumber}";

    Livewire::test(Flight::class)
        ->set('departureAirport', 'KJFK')
        ->set('arrivalAirport', 'EGLL')
        ->set('etops', true)
        ->call('saveFlightConfiguration')
        ->assertHasNoErrors();

    $cached = Cache::get($cacheKey);
    expect($cached)->toBeArray()
        ->and($cached['departureAirport'])->toBe('KJFK')
        ->and($cached['arrivalAirport'])->toBe('EGLL')
        ->and($cached['etops'])->toBe(true);
});

test('flight component clears configuration', function () {
    $cacheKey = "flight_details_{$this->employeeNumber}";

    Cache::put($cacheKey, [
        'departureAirport' => 'KJFK',
        'arrivalAirport' => 'EGLL',
        'etops' => true,
    ], now()->addDays(30));

    Livewire::test(Flight::class)
        ->call('clearFlight')
        ->assertSet('departureAirport', '')
        ->assertSet('arrivalAirport', '')
        ->assertSet('etops', false);

    expect(Cache::has($cacheKey))->toBeFalse();
});

test('flight component validates required fields', function () {
    Livewire::test(Flight::class)
        ->set('departureAirport', '')
        ->set('arrivalAirport', '')
        ->call('saveFlightConfiguration')
        ->assertHasErrors(['departureAirport', 'arrivalAirport']);
});
