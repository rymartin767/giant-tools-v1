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

    // Set up aircraft details (required for checklist)
    Cache::put("aircraft_details_{$this->employeeNumber}", [
        'selectedAirframe' => 'B767-300ER',
        'engines' => 'GE',
        'config' => 'PAX',
    ], now()->addDays(30));

    // Set up flight details in cache so checklist shows main view
    Cache::put("flight_details_{$this->employeeNumber}", [
        'departureAirport' => 'KJFK',
        'arrivalAirport' => 'KLAX',
        'etops' => false,
    ], now()->addDays(30));
});

test('checklist component loads aircraft configuration form when no aircraft configured', function () {
    // Clear aircraft details
    Cache::forget("aircraft_details_{$this->employeeNumber}");

    Livewire::test(Checklist::class)
        ->assertSee('Aircraft Configuration Required')
        ->assertSee('Go to Aircraft Configuration');
});

test('checklist displays main view when flight details are configured', function () {
    Livewire::test(Checklist::class)
        ->assertSee('Pre-Duty Checklist Items')
        ->assertDontSee('Select Departure Airport');
});

test('can toggle checklist items', function () {
    $component = Livewire::test(Checklist::class);

    // Toggle first item of first step
    $component->call('toggleItem', '1.0', 0)
        ->assertDispatched('item-toggled');

    // Check that item is marked as completed
    expect($component->get('completedItems')['1.0'])->toContain(0);
});

test('can navigate between steps', function () {
    $component = Livewire::test(Checklist::class);

    // Go to next step
    $component->call('goToStep', '1.1')
        ->assertSet('currentStep', '1.1');
});

test('progress percentage calculates correctly', function () {
    $component = Livewire::test(Checklist::class);

    // Initially no steps completed
    expect($component->instance()->getProgressPercentage())->toBe(0);
});

test('can reset checklist', function () {
    $component = Livewire::test(Checklist::class);

    // Complete an item
    $component->call('toggleItem', '1.0', 0);

    // Reset checklist
    $component->call('resetChecklist')
        ->assertSet('completedItems', [])
        ->assertSet('currentStep', '1.0');
});
