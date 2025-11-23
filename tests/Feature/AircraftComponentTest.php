<?php

declare(strict_types=1);

use App\Livewire\Aircraft;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

uses(RefreshDatabase::class)->group('aircraft');

beforeEach(function () {
    // Start session to get session ID
    $this->withSession([]);
    $this->sessionId = session()->getId();

    // Set employee number for this session
    $this->employeeNumber = 'test-emp-123';
    Cache::put("employee_number_{$this->sessionId}", $this->employeeNumber, now()->addDays(30));

    // Set up flight details so aircraft component shows main view (required for flightConfigComplete)
    Cache::put("flight_details_{$this->employeeNumber}", [
        'departureAirport' => 'KJFK',
        'arrivalAirport' => 'KLAX',
        'etops' => false,
    ], now()->addDays(30));

    // Set Giants API token for the service
    config(['services.giant_api.token' => 'test-token-123']);

    // Mock HTTP requests to Giants API
    Http::fake([
        'https://api.giantpilots.com/api/v1/airframes*' => Http::response([
            'data' => [
                ['attributes' => ['registration' => 'N12345', 'name' => 'Test Aircraft', 'type' => 'B747', 'manufacturer' => 'Boeing', 'model' => '747-400']],
                ['attributes' => ['registration' => 'N67890', 'name' => 'Another Aircraft', 'type' => 'A320', 'manufacturer' => 'Airbus', 'model' => 'A320-200']],
            ],
        ], 200),
    ]);
});

test('aircraft component renders successfully', function () {
    Livewire::test(Aircraft::class)
        ->assertStatus(200);
});

test('aircraft component loads cached aircraft details', function () {
    $cacheKey = "aircraft_details_{$this->employeeNumber}";
    Cache::put($cacheKey, [
        'selectedAirframe' => 'N12345',
        'engines' => 'GE',
        'config' => 'PAX',
        'supernumeraries' => true,
    ], now()->addDays(30));

    Livewire::test(Aircraft::class)
        ->assertSet('selectedAirframe', 'N12345')
        ->assertSet('engines', 'GE')
        ->assertSet('config', 'PAX')
        ->assertSet('supernumeraries', true);
});

test('aircraft component saves configuration to cache', function () {
    $cacheKey = "aircraft_details_{$this->employeeNumber}";

    Livewire::test(Aircraft::class)
        ->set('selectedAirframe', 'N12345')
        ->set('engines', 'GE')
        ->set('config', 'PAX')
        ->set('supernumeraries', true)
        ->call('saveAircraftConfiguration')
        ->assertHasNoErrors();

    $cached = Cache::get($cacheKey);
    expect($cached)->toBeArray()
        ->and($cached['selectedAirframe'])->toBe('N12345')
        ->and($cached['engines'])->toBe('GE')
        ->and($cached['config'])->toBe('PAX')
        ->and($cached['supernumeraries'])->toBe(true);
});

test('aircraft component clears configuration', function () {
    $cacheKey = "aircraft_details_{$this->employeeNumber}";

    Cache::put($cacheKey, [
        'selectedAirframe' => 'N12345',
        'engines' => 'GE',
        'config' => 'PAX',
        'supernumeraries' => true,
    ], now()->addDays(30));

    Livewire::test(Aircraft::class)
        ->call('clearAirframe')
        ->assertSet('selectedAirframe', '')
        ->assertSet('engines', '')
        ->assertSet('config', '')
        ->assertSet('supernumeraries', false);

    expect(Cache::has($cacheKey))->toBeFalse();
});

test('aircraft component validates required selectedAirframe field', function () {
    Livewire::test(Aircraft::class)
        ->set('selectedAirframe', '')
        ->call('saveAircraftConfiguration')
        ->assertHasErrors(['selectedAirframe']);
});

test('aircraft component allows optional config and engines fields', function () {
    Livewire::test(Aircraft::class)
        ->set('selectedAirframe', 'N12345')
        ->set('engines', '')
        ->set('config', '')
        ->call('saveAircraftConfiguration')
        ->assertHasNoErrors();
});
