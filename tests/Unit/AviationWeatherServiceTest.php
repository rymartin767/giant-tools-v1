<?php

declare(strict_types=1);

use App\Services\AviationWeatherService;
use Illuminate\Support\Facades\Http;

uses(Tests\TestCase::class);

test('setToken returns true for public API', function () {
    $service = new AviationWeatherService;

    expect($service->setToken())->toBeTrue();
});

test('isAuthenticated returns true for public API', function () {
    $service = new AviationWeatherService;

    expect($service->isAuthenticated())->toBeTrue();
});

test('getMetar fetches METAR data successfully', function () {
    Http::fake([
        'aviationweather.gov/api/data/metar*' => Http::response([
            [
                'icaoId' => 'KSFO',
                'rawOb' => 'KSFO 091856Z 28016G24KT 10SM FEW015 SCT250 14/08 A2990 RMK AO2 PK WND 27028/1819 SLP123 T01390078',
                'fltCat' => 'VFR',
                'temp' => 14,
                'wspd' => 16,
            ],
        ], 200),
    ]);

    $service = new AviationWeatherService;
    $result = $service->getMetar('KSFO');

    expect($result['success'])->toBeTrue();
    expect($result['data'])->toBeArray();
    expect($result['data'][0]['icaoId'])->toBe('KSFO');
    expect($result['data'][0]['fltCat'])->toBe('VFR');
});

test('getTaf fetches TAF data successfully', function () {
    Http::fake([
        'aviationweather.gov/api/data/taf*' => Http::response([
            [
                'icaoId' => 'KSFO',
                'rawTAF' => 'TAF KSFO 091720Z 0918/1024 28015G25KT P6SM FEW015 SCT250',
                'issueTime' => '2025-11-09T17:20:00Z',
            ],
        ], 200),
    ]);

    $service = new AviationWeatherService;
    $result = $service->getTaf('KSFO');

    expect($result['success'])->toBeTrue();
    expect($result['data'])->toBeArray();
    expect($result['data'][0]['icaoId'])->toBe('KSFO');
    expect($result['data'][0]['rawTAF'])->toContain('TAF KSFO');
});

test('fetch handles 204 No Content response', function () {
    Http::fake([
        'aviationweather.gov/api/data/metar*' => Http::response(null, 204),
    ]);

    $service = new AviationWeatherService;
    $result = $service->getMetar('KXXX');

    expect($result['success'])->toBeTrue();
    expect($result['data'])->toBeArray();
    expect($result['data'])->toBeEmpty();
});

test('fetch handles API error responses', function () {
    Http::fake([
        'aviationweather.gov/api/data/metar*' => Http::response([
            'error' => 'Invalid station ID',
        ], 400),
    ]);

    $service = new AviationWeatherService;
    $result = $service->getMetar('INVALID');

    expect($result['success'])->toBeFalse();
    expect($result['error'])->toBeArray();
    expect($result['error']['code'])->toBe(400);
    expect($result['error']['message'])->toContain('Invalid station ID');
});

test('fetch handles connection errors', function () {
    Http::fake(fn () => throw new \Illuminate\Http\Client\ConnectionException('Connection timeout'));

    $service = new AviationWeatherService;
    $result = $service->getMetar('KSFO');

    expect($result['success'])->toBeFalse();
    expect($result['error'])->toBeArray();
    expect($result['error']['code'])->toBe(503);
    expect($result['error']['message'])->toContain('temporarily unavailable');
});

test('parseFlightCategory returns correct colors for VFR', function () {
    $service = new AviationWeatherService;
    $result = $service->parseFlightCategory('VFR');

    expect($result['label'])->toBe('VFR');
    expect($result['color'])->toBe('green');
});

test('parseFlightCategory returns correct colors for MVFR', function () {
    $service = new AviationWeatherService;
    $result = $service->parseFlightCategory('MVFR');

    expect($result['label'])->toBe('MVFR');
    expect($result['color'])->toBe('blue');
});

test('parseFlightCategory returns correct colors for IFR', function () {
    $service = new AviationWeatherService;
    $result = $service->parseFlightCategory('IFR');

    expect($result['label'])->toBe('IFR');
    expect($result['color'])->toBe('red');
});

test('parseFlightCategory returns correct colors for LIFR', function () {
    $service = new AviationWeatherService;
    $result = $service->parseFlightCategory('LIFR');

    expect($result['label'])->toBe('LIFR');
    expect($result['color'])->toBe('purple');
});

test('parseFlightCategory handles null category', function () {
    $service = new AviationWeatherService;
    $result = $service->parseFlightCategory(null);

    expect($result['label'])->toBe('N/A');
    expect($result['color'])->toBe('gray');
});

test('parseFlightCategory handles unknown category', function () {
    $service = new AviationWeatherService;
    $result = $service->parseFlightCategory('UNKNOWN');

    expect($result['label'])->toBe('UNKNOWN');
    expect($result['color'])->toBe('gray');
});
