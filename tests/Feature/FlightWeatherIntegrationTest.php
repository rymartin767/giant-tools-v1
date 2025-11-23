<?php

declare(strict_types=1);

use App\Livewire\Flight;
use App\Services\AviationWeatherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Start session to get session ID
    $this->withSession([]);
    $this->sessionId = session()->getId();

    // Set employee number for this session
    $this->employeeNumber = 'test-emp-123';
    Cache::put("employee_number_{$this->sessionId}", $this->employeeNumber, now()->addDays(30));
});

test('METAR displays when departure airport is selected', function () {
    $mockService = Mockery::mock(AviationWeatherService::class);
    $mockService->shouldReceive('getMetar')
        ->with('KSFO')
        ->once()
        ->andReturn([
            'success' => true,
            'data' => [
                [
                    'rawOb' => 'KSFO 091856Z 28016G24KT 10SM FEW015 SCT250 14/08 A2990',
                    'fltCat' => 'VFR',
                ],
            ],
        ]);

    $mockService->shouldReceive('parseFlightCategory')
        ->with('VFR')
        ->once()
        ->andReturn([
            'label' => 'VFR',
            'color' => 'green',
        ]);

    $this->instance(AviationWeatherService::class, $mockService);

    Livewire::test(Flight::class)
        ->set('departureAirport', 'KSFO')
        ->assertSee('METAR')
        ->assertSee('KSFO 091856Z 28016G24KT 10SM FEW015 SCT250 14/08 A2990')
        ->assertSee('VFR');
});

test('TAF displays when arrival airport is selected', function () {
    $mockService = Mockery::mock(AviationWeatherService::class);
    $mockService->shouldReceive('getTaf')
        ->with('KJFK')
        ->once()
        ->andReturn([
            'success' => true,
            'data' => [
                [
                    'rawTAF' => 'TAF KJFK 091720Z 0918/1024 28015G25KT P6SM FEW015 SCT250',
                ],
            ],
        ]);

    $this->instance(AviationWeatherService::class, $mockService);

    Livewire::test(Flight::class)
        ->set('arrivalAirport', 'KJFK')
        ->assertSee('TAF')
        ->assertSee('TAF KJFK 091720Z 0918/1024 28015G25KT P6SM FEW015 SCT250');
});

test('no METAR shown when departure airport not selected', function () {
    Livewire::test(Flight::class)
        ->assertDontSee('METAR');
});

test('no TAF shown when arrival airport not selected', function () {
    Livewire::test(Flight::class)
        ->assertDontSee('TAF');
});

test('displays no METAR available when API returns empty', function () {
    $mockService = Mockery::mock(AviationWeatherService::class);
    $mockService->shouldReceive('getMetar')
        ->with('KXXX')
        ->once()
        ->andReturn([
            'success' => true,
            'data' => [],
        ]);

    $this->instance(AviationWeatherService::class, $mockService);

    Livewire::test(Flight::class)
        ->set('departureAirport', 'KXXX')
        ->assertSee('No METAR data available');
});

test('displays no TAF available when API returns empty', function () {
    $mockService = Mockery::mock(AviationWeatherService::class);
    $mockService->shouldReceive('getTaf')
        ->with('KXXX')
        ->once()
        ->andReturn([
            'success' => true,
            'data' => [],
        ]);

    $this->instance(AviationWeatherService::class, $mockService);

    Livewire::test(Flight::class)
        ->set('arrivalAirport', 'KXXX')
        ->assertSee('No TAF data available');
});

test('handles METAR API errors gracefully', function () {
    $mockService = Mockery::mock(AviationWeatherService::class);
    $mockService->shouldReceive('getMetar')
        ->with('INVALID')
        ->once()
        ->andReturn([
            'success' => false,
            'error' => [
                'code' => 400,
                'message' => 'Invalid station ID',
            ],
        ]);

    $this->instance(AviationWeatherService::class, $mockService);

    Livewire::test(Flight::class)
        ->set('departureAirport', 'INVALID')
        ->assertSee('No METAR data available');
});

test('handles TAF API errors gracefully', function () {
    $mockService = Mockery::mock(AviationWeatherService::class);
    $mockService->shouldReceive('getTaf')
        ->with('INVALID')
        ->once()
        ->andReturn([
            'success' => false,
            'error' => [
                'code' => 400,
                'message' => 'Invalid station ID',
            ],
        ]);

    $this->instance(AviationWeatherService::class, $mockService);

    Livewire::test(Flight::class)
        ->set('arrivalAirport', 'INVALID')
        ->assertSee('No TAF data available');
});

test('displays both METAR and TAF when both airports selected', function () {
    $mockService = Mockery::mock(AviationWeatherService::class);

    $mockService->shouldReceive('getMetar')
        ->with('KSFO')
        ->andReturn([
            'success' => true,
            'data' => [
                [
                    'rawOb' => 'KSFO 091856Z 28016G24KT 10SM FEW015 SCT250 14/08 A2990',
                    'fltCat' => 'VFR',
                ],
            ],
        ]);

    $mockService->shouldReceive('parseFlightCategory')
        ->with('VFR')
        ->andReturn([
            'label' => 'VFR',
            'color' => 'green',
        ]);

    $mockService->shouldReceive('getTaf')
        ->with('KJFK')
        ->andReturn([
            'success' => true,
            'data' => [
                [
                    'rawTAF' => 'TAF KJFK 091720Z 0918/1024 28015G25KT P6SM FEW015 SCT250',
                ],
            ],
        ]);

    $this->instance(AviationWeatherService::class, $mockService);

    Livewire::test(Flight::class)
        ->set('departureAirport', 'KSFO')
        ->set('arrivalAirport', 'KJFK')
        ->assertSee('METAR')
        ->assertSee('TAF')
        ->assertSee('KSFO 091856Z')
        ->assertSee('TAF KJFK 091720Z');
});
